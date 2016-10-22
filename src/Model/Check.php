<?php

namespace Base\Model;

/**
 * Check.class [ HELPER ]
 * Classe responável por manipular e validade dados do sistema!
 * 
 * @copyright (c) 2014, Robson V. Leite UPINSIDE TECNOLOGIA
 */
class Check {

    private static $Data;
    private static $Format;

    /**
     * <b>Verifica E-mail:</b> Executa validação de formato de e-mail. Se for um email válido retorna true, ou retorna false.
     * @param STRING $Email = Uma conta de e-mail
     * @return BOOL = True para um email válido, ou false
     */
    public static function Email($Email) {
        self::$Data = (string) $Email;
        self::$Format = '/[a-z0-9_\.\-]+@[a-z0-9_\.\-]*[a-z0-9_\.\-]+\.[a-z]{2,4}$/';

        if (preg_match(self::$Format, self::$Data)):
            return true;
        else:
            return false;
        endif;
    }

    /**
     * <b>Tranforma URL:</b> Tranforma uma string no formato de URL amigável e retorna o a string convertida!
     * @param STRING $Name = Uma string qualquer
     * @return STRING = $Data = Uma URL amigável válida
     */
    public static function Name($Name) {
        self::$Format = array();
        self::$Format['a'] = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª|';
        self::$Format['b'] = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                 -';

        self::$Data = strtr(utf8_decode($Name), utf8_decode(self::$Format['a']), self::$Format['b']);
        self::$Data = strip_tags(trim(self::$Data));
        self::$Data = str_replace(' ', '-', self::$Data);
        self::$Data = str_replace(array('-----', '----', '---', '--'), '-', self::$Data);

        return strtolower(utf8_encode(self::$Data));
    }

    /**
     * <b>Obter categoria:</b> Informe o name (url) de uma categoria para obter o ID da mesma.
     * @param STRING $category_name = URL da categoria
     * @return INT $category_id = id da categoria informada
     */
    public static function CatByName($CategoryName) {
        $read = new Read;
        $read->ExeRead('ws_categories', "WHERE category_name = :name", "name={$CategoryName}");
        if ($read->getRowCount()):
            return $read->getResult()[0]['category_id'];
        else:
            echo "A categoria {$CategoryName} não foi encontrada!";
            die;
        endif;
    }


    /**
     * <b>Tranforma URL:</b> Tranforma uma string no formato de Action e retorna o a string convertida!
     * @param STRING $Name = Uma string qualquer
     * @return STRING = $Data = Uma URL amigável válida
     */
    public static function Action($Name) {
        self::$Format = array();
        self::$Format['a'] = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª';
        self::$Format['b'] = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                 ';

        self::$Data = strtr(utf8_decode($Name), utf8_decode(self::$Format['a']), self::$Format['b']);
        self::$Data = strip_tags(trim(self::$Data));
        self::$Data = str_replace(' ', '-', self::$Data);
        self::$Data = str_replace(array('-----', '----', '---', '--', '-'), '', self::$Data);
        $action = strtolower(utf8_encode(self::$Data));
        return "{$action}Action";
    }

    /**
     * <b>Checa CPF:</b> Informe um CPF para checar sua validade via algoritimo!
     * @param STRING $CPF = CPF com ou sem pontuação
     * @return BOLEAM = True se for um CPF válido
     */
    public static function CPF($Cpf) {
        self::$Data = preg_replace('/[^0-9]/', '', $Cpf);

        $digitoA = 0;
        $digitoB = 0;

        for ($i = 0, $x = 10; $i <= 8; $i++, $x--) {
            $digitoA += self::$Data[$i] * $x;
        }

        for ($i = 0, $x = 11; $i <= 9; $i++, $x--) {
            if (str_repeat($i, 11) == self::$Data) {
                return false;
            }
            $digitoB += self::$Data[$i] * $x;
        }

        $somaA = (($digitoA % 11) < 2 ) ? 0 : 11 - ($digitoA % 11);
        $somaB = (($digitoB % 11) < 2 ) ? 0 : 11 - ($digitoB % 11);

        if ($somaA != self::$Data[9] || $somaB != self::$Data[10]) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * <b>Tranforma Data:</b> Transforma uma data no formato DD/MM/YY em uma data no formato YYYY-MM-DD!
     * @param STRING $Name = Data em (d/m/Y)
     * @return STRING = $Data = Data no formato YYYY-MM-DD!
     */
    public static function Nascimento($Data) {
        self::$Format = explode(' ', $Data);
        self::$Data = explode('/', self::$Format[0]);

        if (checkdate(self::$Data[1], self::$Data[0], self::$Data[2])):
            self::$Data = self::$Data[2] . '-' . self::$Data[1] . '-' . self::$Data[0];
            return self::$Data;
        else:
            return false;
        endif;
    }

    /**
     * <b>Tranforma TimeStamp:</b> Transforma uma data no formato DD/MM/YY em uma data no formato TIMESTAMP!
     * @param STRING $Name = Data em (d/m/Y) ou (d/m/Y H:i:s)
     * @return STRING = $Data = Data no formato timestamp!
     */
    public static function Data($Data) {
        self::$Format = explode(' ', $Data);
        self::$Data = explode('/', self::$Format[0]);

        if (!checkdate(self::$Data[1], self::$Data[0], self::$Data[2])):
            return false;
        else:
            if (empty(self::$Format[1])):
                self::$Format[1] = date('H:i:s');
            endif;

            self::$Data = self::$Data[2] . '-' . self::$Data[1] . '-' . self::$Data[0] . ' ' . self::$Format[1];
            return self::$Data;
        endif;
    }

    /**
     * <b>Limita os Palavras:</b> Limita a quantidade de palavras a serem exibidas em uma string!
     * @param STRING $String = Uma string qualquer
     * @return INT = $Limite = String limitada pelo $Limite
     */
    public static function Words($String, $Limite, $Pointer = null,$tirar=true) {
       if($tirar):
        self::$Data = strip_tags(trim($String));
       else:
           self::$Data = trim($String);
      endif;
        self::$Format = (int) $Limite;

        $ArrWords = explode(' ', self::$Data);
        $NumWords = count($ArrWords);
        $NewWords = implode(' ', array_slice($ArrWords, 0, self::$Format));

        $Pointer = (empty($Pointer) ? '...' : ' ' . $Pointer );
        $Result = ( self::$Format < $NumWords ? $NewWords . $Pointer : self::$Data );
        return $Result;
    }

    public static function Chars($String, $Limite) {
        self::$Data = strip_tags($String);
        self::$Format = $Limite;
        if (strlen(self::$Data) <= self::$Format) {
            return self::$Data;
        } else {
            $subStr = strrpos(substr(self::$Data, 0, self::$Format), ' ');
            return substr(self::$Data, 0, $subStr) . '...';
        }
    }

    /**
     * <b>Imagem Upload:</b> Ao executar este HELPER, ele automaticamente verifica a existencia da imagem na pasta
     * uploads. Se existir retorna a imagem redimensionada!
     * @return HTML = imagem redimencionada!
     */
    public static function Image($ImageUrl, $ImageDesc, $ImageW = null, $ImageH = null, $class = "logo") {

        self::$Data = $ImageUrl;
        if (!is_dir(self::$Data)):
            $patch = '/dist/';
            $imagem = self::$Data;
            $tagImage="";
            if($ImageW)
            {
                $tagImage.="&w={$ImageW}";
            }
            
            if($ImageH)
            {
                 $tagImage.="&h={$ImageH}";
            }
            
            if(!empty($class))
            {
                 $class=" class=\"{$class}\"";
            }
            return "<img id='img-preview' src='/dist/tim.php?src={$patch}{$imagem}{$tagImage}' {$class} alt=\"{$ImageDesc}\" title=\"{$ImageDesc}\" />";
        else:
            return false;
        endif;
    }

    /**
     * <b>Imagem Upload:</b> Ao executar este HELPER, ele automaticamente verifica a existencia da imagem na pasta
     * uploads. Se existir retorna a imagem redimensionada!
     * @return HTML = imagem redimencionada!
     */
    public static function ImageCaminho($ImageUrl,$w="420",$h="330") {

        self::$Data = $ImageUrl;
        if (!is_dir(self::$Data)):
            $patch = '';
            $imagem = self::$Data;
            return "/dist/tim.php?src=/dist/{$imagem}&w={$w}&h={$h}";
        else:
            return false;
        endif;
    }

    /**
     * Pesquisa imagem do aluno, se existir retorna, se não. Retorna o noavatar da pasta tpl/img
     * @return STRING = URL do avatar
     */
    public static function Avatar($AlunoId) {
        $Read = new Read;
        $Read->ExeRead(CF_ALUNOS, "WHERE alunoId = :alId", "alId={$AlunoId}");

        if (!$Read->getResult()):
            return false;
        else:
            self::$Data = $Read->getResult()[0];
            self::$Format = 'uploads/' . self::$Data['alunoAvatar'];

            if ((file_exists(self::$Format) && !is_dir(self::$Format)) || (file_exists('../' . self::$Format) && !is_dir('../' . self::$Format))):
                return BASE . '/' . self::$Format;

            elseif (self::$Data['alunoSexo'] == 'f'):
                self::$Format = BASE . '/tpl/_img/f_noavatar.jpg';
                return self::$Format;

            elseif (self::$Data['alunoSexo'] == 'm'):
                self::$Format = BASE . '/tpl/_img/m_noavatar.jpg';
                return self::$Format;
            endif;
        endif;
    }

    /**
     * PEGA NOME DO AGENT DE 
     * @return STRING Agent Name
     */
    public static function Agent() {
        self::$Data = $_SESSION['useronline']['online_agent'];
        if (empty(self::$Data)):
            return null;
        else:
            if (strpos(self::$Data, 'Chrome')):
                return 'Chrome';
            elseif (strpos(self::$Data, 'Firefox')):
                return 'Firefox';
            elseif (strpos(self::$Data, 'MSIE') || strpos(self::$Data, 'Trident/')):
                return 'IE';
            else:
                return 'Outros';
            endif;
        endif;
    }

    public static function getBase() {
        if (filter_has_var(INPUT_SERVER, "DOCUMENT_ROOT")) {
            $servername = filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
        } else {
            if (isset($_SERVER["DOCUMENT_ROOT"])) {
                $servername = filter_var($_SERVER["DOCUMENT_ROOT"], FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
            } else {
                $servername = "";
            }
        }
        return $servername;
    }

    public static function getInfoImage($imageUrl = "") {
        $servername = self::getBase();
        if (file_exists(sprintf("%s/uploads/%s", $servername, $imageUrl))) {

            $servername = self::getBase();

            return getimagesize(sprintf("%s/uploads/%s", $servername, $imageUrl));
        }
        else
        {
             return getimagesize(sprintf("%s/img/%s", $servername, "no_image.jpg"));
        }
        return FALSE;
    }

    public static function NewPass($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false) {
        $lmin = 'abcdefghijklmnopqrstuvwxyz';
        $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num = '1234567890';
        $simb = '!@#$%*-';
        $retorno = '';
        $caracteres = '';

        $caracteres .= $lmin;
        if ($maiusculas):
            $caracteres .= $lmai;
        endif;
        if ($numeros):
            $caracteres .= $num;
        endif;
        if ($simbolos):
            $caracteres .= $simb;
        endif;

        $len = strlen($caracteres);
        for ($n = 1; $n <= $tamanho; $n++) {
            $rand = mt_rand(1, $len);
            $retorno .= $caracteres[$rand - 1];
        }
        return $retorno;
    }

    public static function get_full_url() {
        $https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0 ||
                !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
                strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0;
        return
                ($https ? 'https://' : 'http://') .
                (!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] . '@' : '') .
                (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'] .
                        ($https && $_SERVER['SERVER_PORT'] === 443 ||
                        $_SERVER['SERVER_PORT'] === 80 ? '' : ':' . $_SERVER['SERVER_PORT']))) .
                substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
    }
    
    public static function debug($param, $tipo = "d") {
        echo '<PRE>';
        if ($tipo == "d") {
            var_dump($param);
        } else {
            print_r($param);
        }
        echo '</PRE>';
    }

    /**
     * @param \SplFileInfo $file
     * @return bool
     */
    public static function isFileHidden(\SplFileInfo $file) {
        $basename = $file->getBasename();
        return strpos($basename, '.') >0;
    }

}
