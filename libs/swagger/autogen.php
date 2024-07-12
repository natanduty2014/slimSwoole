<?php

require_once '../../vendor/autoload.php';

$swagger = array(
    "openapi" => "3.0.0",
    "info" => array(
        "title" => NAME,
        "description" => DESCRIPTION,
        "version" => VERSION
    ),
    "servers" => array(
        array(
            "url" => "http://localhost:9502"
        )
    ),
    "tags" => array(),
    "paths" => array(),
    "components" => array(
        "schemas" => new stdClass(),
        "securitySchemes" => array(
            "bearerAuth" => array(
                "type" => "http",
                "scheme" => "bearer",
                "bearerFormat" => "JWT"
            )
        ),
        "parameters" => new stdClass(),
        "responses" => array(
            "200" => array(
                "description" => "Success"
            ),
            "400" => array(
                "description" => "Bad request"
            ),
            "401" => array(
                "description" => "Unauthorized"
            ),
            "500" => array(
                "description" => "Internal Server Error"
            )
        ),
        "requestBodies" => new stdClass(),
    )
);

$routes = array();
$fileContent = file_get_contents('../../app/src/Routes/web.php');

// Ignorar linhas comentadas
$fileContent = preg_replace('/\/\/.*|\/\*[\s\S]*?\*\//', '', $fileContent);

preg_match_all('/\$app->group\(\'(.*?)\',\s*function\s*\(RouteCollectorProxy\s*\$group\)\s*{/', $fileContent, $groupMatches, PREG_SET_ORDER);

preg_match_all('/\$group->(get|post|put|delete)\(\'(.*?)\',\s*(.*?)::class\s*\.\s*\'(.*?)\'\)(->add\(Authorization::class\s*\.\s*\':Authorization\'\))?;/', $fileContent, $groupRouterMatches, PREG_SET_ORDER);

foreach ($groupMatches as $match) {
    $tags = array(
        "name" => $match[1],
        "description" => "Grupo de rotas para " . $match[1],
    );
    array_push($swagger['tags'], $tags);
    $routes[$match[1]] = [];
}

function getClassProperties($className) {
    if (!class_exists($className)) {
        //echo "Class $className does not exist.\n"; // Mensagem de depuração
        return [];
    }

    //echo "Class $className exists.\n"; // Mensagem de depuração
    $reflectionClass = new ReflectionClass($className);
    $properties = $reflectionClass->getProperties();
    $schema = array();

    foreach ($properties as $property) {
        $type = $property->getType();
        $schemaType = 'string'; // Valor padrão

        if ($type !== null) {
            $typeName = $type->getName();
            switch ($typeName) {
                case 'int':
                case 'integer':
                    $schemaType = 'integer';
                    break;
                case 'float':
                case 'double':
                    $schemaType = 'number';
                    break;
                case 'bool':
                case 'boolean':
                    $schemaType = 'boolean';
                    break;
                case 'array':
                    $schemaType = 'array';
                    break;
            }
        }

        // Verificar se a propriedade é um array com esquema definido
        if ($schemaType == 'array' && $property->isDefault()) {
            $property->setAccessible(true);
            $defaultValue = $property->getDefaultValue();
            if (is_array($defaultValue)) {
                $itemsSchema = array();
                foreach ($defaultValue as $key => $value) {
                    $itemsSchema[$key] = array("type" => gettype($value));
                }
                $schema[$property->getName()] = array(
                    "type" => "object",
                    "properties" => $itemsSchema
                );
            } else {
                $schema[$property->getName()] = array("type" => $schemaType);
            }
        } else {
            $schema[$property->getName()] = array("type" => $schemaType);
        }
    }

    return $schema;
}

function getDataPropertySchema($className, $propertyName) {
    if (!class_exists($className)) {
        return [];
    }

    $reflectionClass = new ReflectionClass($className);
    $property = $reflectionClass->getProperty($propertyName);

    $type = $property->getType();
    $schemaType = 'string'; // Valor padrão

    if ($type !== null) {
        $typeName = $type->getName();
        switch ($typeName) {
            case 'array':
                $schemaType = 'object';
                break;
        }
    }

    // Verificar se a propriedade é um array com esquema definido
    if ($schemaType == 'object' && $property->isDefault()) {
        $property->setAccessible(true);
        $defaultValue = $property->getDefaultValue();
        if (is_array($defaultValue)) {
            $itemsSchema = array();
            foreach ($defaultValue as $key => $value) {
                $itemsSchema[$key] = array("type" => ($value === "int") ? "integer" : gettype($value));
            }
            return array(
                "type" => "object",
                "properties" => $itemsSchema
            );
        }
    }

    return array("type" => $schemaType);
}

foreach ($groupRouterMatches as $match) {
    $method = $match[1];
    $path = $match[2];
    $controllerClass = 'App\\Controllers\\' . $match[3]; // Usar namespace completo
    $controllerMethod = $match[4];
    $requiresAuth = isset($match[5]) && $match[5] !== ''; // Verifica se a rota requer autorização
    $groupPath = "/v1/api/" . $match[3].'/';
    $groupPath2 = "/v1/api/" . $match[3] .'/'. $path;

    if (!isset($swagger['paths'][$groupPath2])) {
        $swagger['paths'][$groupPath2] = [];
    }

    $swaggerPathItem = array(
        "tags" => array($groupPath),
        "operationId" => $controllerClass . "::" . $controllerMethod,
        "responses" => array(
            ($method === 'post' ? "201" : "200") => array(
                "description" => "Operação bem-sucedida"
                )
                )
            );
        
            // Adicionar parâmetros de caminho se existir {id} na rota
            if (strpos($path, '{id}') !== false) {
                $swaggerPathItem['parameters'][] = array(
                    "name" => "id",
                    "in" => "path",
                    "required" => true,
                    "schema" => array(
                        "type" => "integer"
                    ),
                    "description" => "ID do recurso"
                );
            }
        
            // Adicionar parâmetros de caminho se existir {slug} na rota
            if (strpos($path, '{slug}') !== false) {
                $swaggerPathItem['parameters'][] = array(
                    "name" => "slug",
                    "in" => "path",
                    "required" => true,
                    "schema" => array(
                        "type" => "string"
                    ),
                    "description" => "Slug do recurso"
                );
            }
        
            // Adicionar corpo de requisição para métodos POST e PUT
            if ($method === 'post' || $method === 'put') {
                $dataSchema = getDataPropertySchema($controllerClass, 'data');
                $paginationSchema = getDataPropertySchema($controllerClass, 'pagination');
                if (!empty($dataSchema) || !empty($paginationSchema)) {
                    $schemaName = (new ReflectionClass($controllerClass))->getShortName();
                    if (!isset($swagger['components']['schemas']->$schemaName)) {
                        $swagger['components']['schemas']->$schemaName = new stdClass();
                    }
                    $swagger['components']['schemas']->$schemaName = array(
                        "type" => "object",
                        "properties" => array(
                            "data" => $dataSchema,
                            "pagination" => $paginationSchema,
                        )
                    );
        
                    $swaggerPathItem['requestBody'] = array(
                        "required" => true,
                        "content" => array(
                            "application/json" => array(
                                "schema" => array(
                                    "type" => "object",
                                    "properties" => array(
                                        "data" => $dataSchema
                                        // "pagination" => $paginationSchema
                                    )
                                )
                            )
                        )
                    );
                }
            }
        
            // Adicionar segurança se a rota requer autorização
            if ($requiresAuth) {
                $swaggerPathItem['security'] = array(
                    array(
                        "bearerAuth" => array()
                    )
                );
            }
        
            $swagger['paths'][$groupPath2][strtolower($method)] = $swaggerPathItem;
        }
        
        file_put_contents('swagger.json', json_encode($swagger, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        
        //message success (color green)
        print_r("\033[32mArquivo swagger.json gerado com sucesso!\033[0m \n");
        ?>
   