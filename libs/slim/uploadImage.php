<?php

namespace Lib\Slim;

class uploadImage{
    /**
     * @param string $file
     * @param string $src
     * @return string
     */
    /*//convert base64 to file
    $data = $request->getParsedBody();
    upload::base64ToFile($data['file'], './public/uploads/', 'png');
    */
    static public function base64ToFile($file, $src, $type) {
        try {
            //$type = explode("/", explode(";", $file)[0])[1];
            $data = explode(',', $file)[1]; // Remove o cabeçalho "data:image/png;base64,"
            $decodedData = base64_decode($data); // Decodifica a string base64 em dados binários
            $file_name = $type . '_' . md5(time() . rand(0, 9999)) . '.' . $type;
            file_put_contents($src . $file_name, $decodedData); // Salva os dados binários no arquivo
            //return file route
            //remove . from string
            $src = substr($src, 1);
            return $src.$file_name;
        } catch(\Exception $e) {
            return json_encode($e->getMessage());
        }
    }

     /**
     * @param string $imagem
     * @param string $src
     * @return string
     */
    static public function image($imagem, $src)
    {
        if ($imagem) {
            try {
                list($type, $imagem) = explode(';', $imagem);
                list(, $imagem) = explode(',', $imagem);
                $imagem = base64_decode($imagem);
                $imagem_nome = md5(time() . rand(0, 9999)) . '.webp';
                file_put_contents($src . $imagem_nome, $imagem);
                $imgp = self::compressIMG($src . $imagem_nome, $src . $imagem_nome, 90);
                //remover o primeiro ponto da string
                $imgp = substr($imgp, 1);
                return (string) $imgp;
            } catch (\Exception $e) {
                return json_encode($e->getMessage());
            }
        } else {
            return 'false';
        }
    }

     /**
     * @param string $source
     * @param string $destination
     * @param int $quality
     * @return string
     */
    static private function compressIMG($source, $destination, $quality)
    {

        $info = getimagesize($source);

        if ($info['mime'] == 'image/jpeg')
            $image = imagecreatefromjpeg($source);

        elseif ($info['mime'] == 'image/gif')
            $image = imagecreatefromgif($source);

        elseif ($info['mime'] == 'image/png')
            $image = imagecreatefrompng($source);

        imagewebp($image, $destination, $quality);

        return $destination;
    }
}