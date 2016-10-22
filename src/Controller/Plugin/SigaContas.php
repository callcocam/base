<?php
/**
 * Created by PhpStorm.
 * User: Call
 * Date: 30/07/2016
 * Time: 14:01
 */

namespace Base\Controller\Plugin;


use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class SigaContas extends AbstractPlugin{


    public function juros($post) {
        $v1 = $post ['atual'];
        $v2 = $post ['juro'];
        $op = $post ['op'];
        return $this->Calcular ( $v1, $this->Calcular ( $v1, $v2, "%" ), "+" );
    }
    public function descontos($post) {
        $v1 = $this->Calcular($post ['atual'],100,"/");
        $v2 =$this->Calcular($this->read($post ['descontos']),$v1,"*");
        return $this->Calcular ( $post ['atual'],$v2, "-" );
    }



    public function  float($data)
    {
        foreach ($data as $key=> $val) {
            if (is_float($val))
                $data[$key]=$this->read($val);
            else{
                $data[$key]=$val;
            }
        }
        return $data;
    }

    public function read($post) {
        //$res=str_replace ( ",", "", $post );
        return @number_format($post,2, ",", "."  );
    }
    public function write($post) {
        $source = array('.', ',');
        $replace = array('', '.');
        $valor = str_replace($source, $replace, $post); //remove os pontos e substitui a virgula pelo ponto
        return $valor; //retorna o valor formatado para gravar no banco
    }

    public function  decimal($data)
    {

        foreach ($data as $key=> $val) {
            if(!is_array($val)){
                if (preg_match ("/([0-9])+([,]([0-9])*)$/", $val)) {

                    $data[$key]=$this->write($val);
                }
                else
                {
                    $data[$key]=trim($val);
                }
            }
            else
                $data[$key]=$val;
        }
        return $data;
    }

    public function Calcular($v1,$v2,$op) {
        $v1 = str_replace ( ".", "", $v1);
        $v1 = str_replace ( ",", ".", $v1);
        $v2 = str_replace ( ".", "",$v2 );
        $v2 = str_replace ( ",", ".",$v2);
        switch ($op) {
            case "+":
                $r = $v1 + $v2;
                break;
            case "-":
                $r = $v1 - $v2;
                break;
            case "*":
                $r = $v1 * $v2;
                break;
            case "%":
                $bs = $v1 / 100;
                $j = $v2 * $bs;
                $r = $v1 + $j;
                break;
            case "/":
                @$r = @$v1 / $v2;
                break;
            case "tj":
                $bs = $v1 / 100;
                $j = $v2 * $bs;
                $r = $j;
                break;
            default :
                $r = $v1;
                break;
        }
        $ret = @number_format ( $r, 2, ",", "." );
        return $ret;
    }

    public function form_conferir($v1, $v2, $op) {
        $v1 = str_replace(".", "", $v1);
        $v1 = str_replace(",", ".", $v1);
        $v2 = str_replace(".", "", $v2);
        $v2 = str_replace(",", ".", $v2);
        switch ($op) {
            case "!":
                if ($v1 != $v2)
                    $ret = TRUE;
                else
                    $ret = FALSE;
                break;
            case ">":
                if ($v1 > $v2)
                    $ret = TRUE;
                else
                    $ret = FALSE;
                break;
            case ">=":
                if ($v1 >= $v2)
                    $ret = TRUE;
                else
                    $ret = FALSE;
                break;
            case "<=":
                if ($v1 <= $v2)
                    $ret = TRUE;
                else
                    $ret = FALSE;
                break;
            case "<":
                if ($v1 < $v2)
                    $ret = TRUE;
                else
                    $ret = FALSE;
                break;
            case "=":
                if ($v1 == $v2)
                    $ret = TRUE;
                else
                    $ret = FALSE;
                break;
            default :
                $ret = TRUE;
                break;
        }

        return $ret;
    }

    public function margem_lucro($post) {
        $c =$this->Calcular($post['pago'],"100","/");// valor($v1, 100, "/");
        $df = $this->Calcular($post['valor'],$post['pago'],"-");//valor($v2, $v1, "-");
        return $this->Calcular($df,$c,"/");///($df, $c, "/");
    }
    public function Calculajuros($valo_inicial,$dt_vencimento,$retorno='total',$taxa="0,03")
    {
        $valor_doc=$valo_inicial;
        $juro=0;

        $resultado=array();
        $dt_vencimento=str_replace( "/", "-",$dt_vencimento);
        $dias=$this->form_diasEntreData($dt_vencimento,date('d-m-Y'),0);
        if ($dias <= 0) {
            $resultado['total']=$valor_doc;
            $resultado['taxa'] = 0;
            $resultado['juro'] =0;
            $resultado['dias'] =0;
        } else {
            //se estiver atrasa e não foi paga soma o juro
            $juro=$this->Calcular($dias,$taxa,'*');
            // mais o valor real da parcela
            $resultado['total']= $this->Calcular($valor_doc, $juro, "%");
            $resultado['taxa'] = $this->Calcular($valor_doc, $juro, "tj");
            $resultado['juro'] = $this->Calcular($resultado['total'],$valor_doc, "-");
            $resultado['dias'] =(int)$dias;
        }//Moeda(($valor_doc * 2) / 100);

        return $resultado[$retorno];

    }

    public function form_extenso($valor = 0, $maiusculas = false) {

        $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
        $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões");

        $c = array("", "cem", "duzentos", "trezentos", "quatrocentos",
            "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
        $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta",
            "sessenta", "setenta", "oitenta", "noventa");
        $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze",
            "dezesseis", "dezesete", "dezoito", "dezenove");
        $u = array("", "um", "dois", "três", "quatro", "cinco", "seis",
            "sete", "oito", "nove");

        $z = 0;
        $rt = "";
        $valor = str_replace(",", ".", $valor);
        $valor = @number_format($valor, 2, ".", ".");
        $inteiro = explode(".", $valor);
        for ($i = 0; $i < count($inteiro); $i++)
            for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++)
                $inteiro[$i] = "0" . $inteiro[$i];

        $fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
        for ($i = 0; $i < count($inteiro); $i++) {
            $valor = $inteiro[$i];
            $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
            $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
            $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

            $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd &&
                    $ru) ? " e " : "") . $ru;
            $t = count($inteiro) - 1 - $i;
            $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
            if ($valor == "000")
                $z++; elseif ($z > 0)
                $z--;
            if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
                $r .= (($z > 1) ? " de " : "") . $plural[$t];
            if ($r)
                $rt = $rt . ((($i > 0) && ($i <= $fim) &&
                        ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
        }

        if (!$maiusculas) {
            return($rt ? $rt : "zero");
        } else {

            if ($rt)
                $rt = ereg_replace(" E ", " e ", ucwords($rt));
            return (($rt) ? ($rt) : "Zero");
        }




    }

    public function form_data_hora($data,$tipo=0) {
        /* Primeiramente temos que definir um nome pra variavel que pega a DATA/HORA do computador.
         Vamos dar, o nome de horario.
        */

        error_reporting ( 0 );
        $horario =$this->form_convdata($data,$tipo);// date("Y-m-d H:i:s");

        /* pronto, agora a DATA/HORA do PC , esta armazenada nesta variavel no formato timestamp (AAAA-MM-DD HH:ii:ss).
         agora vamos decompor esta variavel.. */

        $month = substr($horario, 5, 2);
        $date = substr($horario, 8, 2);
        $year = substr($horario, 0, 4);
        $hour = substr($horario, 11, 2);
        $minutes = substr($horario, 14, 2);
        $seconds = substr($horario, 17, 4);

        $data = date("D M j G:i:s T Y", mktime($hour, $minutes, $seconds, $month, $date, $year));

        /* usei substr para restringir o numero de caracter desejado.
         se dermos um echo na $data - teremos no formato padrao a data assim:
        Mon Aug 28 17:53:45 Hora oficial do Brasil 2006
        mas queremos transformar isto em, Segunda Feira 28 Agosto 17:53, entao criaremos agora a variavel, que pegara no banco de dados o dia da semana. */

        $divi = explode(" ", $data);
        $dia_semana_eng = $divi[0];

        $mes = $divi[1];
        $dia = $divi[2];
        $horario = $divi[3];

        switch ($dia_semana_eng) {
            case 'Mon' :
                $dia_semana_port = 2;
                $text = "Segunda-Feira";
                break;

            case 'Tue' :
                $dia_semana_port = 3;
                $text = "Terça-Feira";
                break;

            case 'Wed' :
                $dia_semana_port = 4;
                $text = "Quarta-Feira";
                break;

            case 'Thu' :
                $dia_semana_port = 5;
                $text = "Quinta-Feira";
                break;

            case 'Fri' :
                $dia_semana_port = 6;
                $text = "Sexta-Feira";
                break;

            case 'Sat' :
                $text = "Sabado";
                $dia_semana_port = 7;
                break;

            case 'Sun' :
                $text = "Domingo";
                $dia_semana_port = 1;
                break;
        }

        /* variavel, $dia_semana_pt = busca o valor do dia do banco, e passa para o portugues.
         vamos criar tambem uma variavel que "arrume" a data no formato portugues (DD/MM/AAAA)
        esta é a parte mais facil */

        // echo $date . "/" . $month . "/" . $year;
        $array_mes = array("01" => "Janeiro",
            "02" => "Fevereiro",
            "03" => "Março",
            "04" => "Abril",
            "05" => "Maio",
            "06" => "Junho",
            "02" => "Julho",
            "08" => "Agosto",
            "09" => "Setembro",
            "10" => "Outubro",
            "11" => "novembro",
            "12" => "Dezembro");
        return $text . ",  " . $date . " de " . $array_mes[$month] . " de $year";
    }

    //$timestamp = strtotime ($dataparcela . "+28 days" );
    public function form_diasEntreData($date_ini, $date_end,$tipo=0) {

        $data_ini = strtotime($this->form_convdata($this->form_convdata($date_ini,$tipo), 2)); //data inicial '29 de julho de 2003'
        $hoje =$this->form_convdata($date_end,$tipo); //date("m/d/Y"); // data atual
        $foo = strtotime($hoje); // transforma data atual em segundos (eu acho)
        $dias = ($foo - $data_ini) / 86400; //calcula intervalo
        return (int) $dias;

    }
    public function form_convdata($dataform, $tipo=0) {
        if ($tipo == 0) {
            $datatrans = explode("-", $dataform);
            @$data = "$datatrans[2]-$datatrans[1]-$datatrans[0]";
        } elseif ($tipo == 1) {
            $datatrans = explode("-", $dataform);
            $data = "$datatrans[2]/$datatrans[1]/$datatrans[0]";
        } elseif ($tipo == 2) {
            $datatrans = explode("-", $dataform);
            $data = "$datatrans[1]/$datatrans[2]/$datatrans[0]";
        } elseif ($tipo == 3) {
            $datatrans = explode("/", $dataform);
            $data = "$datatrans[0]-$datatrans[1]-$datatrans[2]";
        }

        return @$data;
    }

    public function form_convdata_pt($dataform, $tipo) {

        $datatrans = explode($tipo, $dataform);
        @$data = "$datatrans[2]-$datatrans[1]-$datatrans[0]";
        return @$data;
    }
    // Formata data dd/mm/aaaa para aaaa-mm-dd
    function datasql($databr) {
        if (!empty($databr)){
            $p_dt = explode('-',$databr);
            $data_sql = $p_dt[2].'-'.$p_dt[1].'-'.$p_dt[0];
            return $data_sql;
        }
    }
    public	function mask($val, $mask)
    {
        $maskared = '';
        $k = 0;
        for($i = 0; $i<=strlen($mask)-1; $i++)
        {
            if($mask[$i] == '#')
            {
                if(isset($val[$k]))
                    $maskared .= $val[$k++];
            }
            else
            {
                if(isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }

} 