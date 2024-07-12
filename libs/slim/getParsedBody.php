<?php

namespace lib\slim;

class getParsedBody
{
    private $data;

    public function filter($data)
    {
        if (is_null($data)) {
            throw new \Exception(\json_encode(['error' => 'Invalid data is null', 'status' => 422]));
        }
        $this->data = filter_var($data);
        return $this;
    }

    public function isArray()
    {
        if (!\is_array($this->data)) {
            throw new \Exception(\json_encode(['error' => 'Invalid array', 'status' => 422]));
        }
        return $this;
    }

    public function isString()
    {
        if (!\is_string($this->data)) {
            throw new \Exception(json_encode(['error' => 'Invalid string', 'status' => 400]));
        }
        return $this;
    }

    public function isInt()
    {
        if (!\is_int($this->data)) {
            throw new \Exception(json_encode(['error' => 'Invalid string', 'status' => 400]));
        }
        return $this;
    }

    public function isStringInput($key)
    {
        if (!isset($this->data[$key])) {
            throw new \Exception(json_encode(['error' => $key . ' Invalid key is missing', 'status' => 422]));
        }
        if (\is_int($this->data[$key])) {
            throw new \Exception(json_encode(['error' => $key . ' Invalid is int', 'status' => 400]));
        }
        if (!\is_string($this->data[$key])) {
            throw new \Exception(json_encode(['error' => $key . ' Invalid string', 'status' => 400]));
        }
        return $this;
    }


    public function arrayToJson()
    {
        if (!\is_array($this->data)) {
            throw new \Exception(\json_encode(['error' => 'Invalid, not an array', 'status' => 422]));
        }
        $this->data = json_encode($this->data);
        return $this;
    }

    public function jsonToArray($data)
    {
        if (!\is_string($data)) {
            throw new \Exception(\json_encode(['error' => 'Invalid, not a string', 'status' => 422]));
        }
        $this->data = \json_decode($data, true);
        return $this;
    }

    public function isValidDate($key)
    {
        if (!isset($this->data[$key])) {
            throw new \Exception(json_encode(['error' => 'Date key is missing in the array. isValidDate not key on array => ' . $key, 'status' => 422]));
        }

        $date = $this->data[$key];
        $format = 'Y-m-d';
        $d = \DateTime::createFromFormat($format, $date);
        if ($d && $d->format($format) === $date) {
            return $this;
        } else {
            throw new \Exception(json_encode(['error' => 'Invalid date format.', 'status' => 422]));
        }
    }

    public function nullParams($data)
    {
        if (is_null($data) || empty($data)) {
            throw new \Exception(json_encode(['error' => 'Invalid data is null or empty', 'status' => 422]));
        }
        $this->data = $data;
        return $this;
    }

    public function strlen($key, int $quant){
        if (!isset($this->data[$key]) | !isset($quant)) {
            throw new \Exception(json_encode(['error' => $key . ' is missing in the array. strlen not key on array', 'status' => 422]));
        }

        if (strlen($this->data[$key]) > (int)$quant) {
            throw new \Exception(\json_encode(['error' => $key .' is too long', 'status' => 422]));
        }
        return $this;
    }
    

    public function removeEmptySpaces($key)
    {
        if (!isset($this->data[$key])) {
            throw new \Exception(\json_encode(['error' => $key . ' is missing in the array. removeEmptySpaces not key on array', 'status' => 422]));
        }
        $this->data[$key] = str_replace(' ', '', $this->data[$key]);
        return $this;
    }

    public function validInputEmpty($key){
        if (!isset($this->data[$key])) {
            throw new \Exception(json_encode(['error' => $key . ' is missing in the array. validInputEmpty not key on array', 'status' => 422]));
        }
        if (empty($this->data[$key])) {
            throw new \Exception(json_encode(['error' => $key . ' is empty in the array. validInputEmpty not key on array', 'status' => 400]));
        }
        return $this;

    }

    public function analyseAray($key, int $type = 3)
    {
       //array = ""
       if($this->data[$key] == ""){
        return $this;
       }
        if (isset($this->data[$key])) {
            if (!is_array($this->data[$key])) {
                throw new \Exception(json_encode(['error' => $key . ' is invalid array. analyseAray not key on array or invalid format', 'status' => 400]));
            }
            for ($i = 0, $len = count($this->data[$key]); $i < $len; $i++) {
                if ($type == 1 && !is_string($this->data[$key][$i])) {
                    throw new \Exception(json_encode(['error' => $key . ' Invalid stack item format. is require type string and no int in input', 'status' => 400]));
                }
                if ($type == 2 && !is_int($this->data[$key][$i])) {
                    throw new \Exception(json_encode(['error' => $key . ' Invalid stack item format. is require type int and no string in input', 'status' => 400]));
                }
                if ($type == 3) {
                    return $this;
                }
            }
        } else {
            throw new \Exception(json_encode(['error' => $key . ' is missing in the array.', 'status' => 422]));
        }
        return $this;
    }


    public function getData()
    {
        return $this->data;
    }
}
