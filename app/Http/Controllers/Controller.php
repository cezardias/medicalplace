<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\SalasImagens;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /*
    public function slugfy($string) {
        return preg_replace('~[^\pL\d]+~u', '-', strtolower($this->remove_accent($string)));
    }
    */
    
    public function gravaImagensSala($arquivos,$sala_id) {

        foreach ($arquivos as $k => $a) {
            $upload_imagem = $this->uploadArquivo($a);
            $imagem = new SalasImagens();
            $imagem->sala_id = $sala_id;
            $imagem->caminho = $upload_imagem['caminho'];
            $imagem->tipo = 'adicional';
            if ($k == 0)
                $imagem->tipo = 'capa';

            if (!$imagem->save())
                return false;
        }
        return true;
    }

    public function uploadArquivo($arquivo) {
        
        $caminho = 'img_sala';

        $mime_type = $arquivo->getMimeType();
        $nome_original = $arquivo->getClientOriginalName();

        $arquivo->move($caminho,$arquivo->getClientOriginalName());

        return array(
            'caminho' => "/".$caminho."/".$nome_original,
            'extensao' => $mime_type
        );
        
        return false;
    }


    public function isCpfValid($cpf)
    {
       //Etapa 1: Cria um array com apenas os digitos numéricos, isso permite receber o cpf em diferentes formatos como "000.000.000-00", "00000000000", "000 000 000 00" etc...
       $j=0;
       for($i=0; $i<(strlen($cpf)); $i++)
           {
               if(is_numeric($cpf[$i]))
                   {
                       $num[$j]=$cpf[$i];
                       $j++;
                   }
           }
       //Etapa 2: Conta os dígitos, um cpf válido possui 11 dígitos numéricos.
       if(count($num)!=11)
           {
               $isCpfValid=false;
           }
       //Etapa 3: Combinações como 00000000000 e 22222222222 embora não sejam cpfs reais resultariam em cpfs válidos após o calculo dos dígitos verificares e por isso precisam ser filtradas nesta parte.
       else
           {
               for($i=0; $i<10; $i++)
                   {
                       if ($num[0]==$i && $num[1]==$i && $num[2]==$i && $num[3]==$i && $num[4]==$i && $num[5]==$i && $num[6]==$i && $num[7]==$i && $num[8]==$i)
                           {
                               $isCpfValid=false;
                               break;
                           }
                   }
           }
       //Etapa 4: Calcula e compara o primeiro dígito verificador.
       if(!isset($isCpfValid))
           {
               $j=10;
               for($i=0; $i<9; $i++)
                   {
                       $multiplica[$i]=$num[$i]*$j;
                       $j--;
                   }
               $soma = array_sum($multiplica);	
               $resto = $soma%11;			
               if($resto<2)
                   {
                       $dg=0;
                   }
               else
                   {
                       $dg=11-$resto;
                   }
               if($dg!=$num[9])
                   {
                       $isCpfValid=false;
                   }
           }
       //Etapa 5: Calcula e compara o segundo dígito verificador.
       if(!isset($isCpfValid))
           {
               $j=11;
               for($i=0; $i<10; $i++)
                   {
                       $multiplica[$i]=$num[$i]*$j;
                       $j--;
                   }
               $soma = array_sum($multiplica);
               $resto = $soma%11;
               if($resto<2)
                   {
                       $dg=0;
                   }
               else
                   {
                       $dg=11-$resto;
                   }
               if($dg!=$num[10])
                   {
                       $isCpfValid=false;
                   }
               else
                   {
                       $isCpfValid=true;
                   }
           }
       //Trecho usado para depurar erros.
       /*
       if($isCpfValid==true)
           {
               echo "<font color="GREEN">Cpf é Válido</font>";
           }
       if($isCpfValid==false)
           {
               echo "<font color="RED">Cpf Inválido</font>";
           }
       */
       //Etapa 6: Retorna o Resultado em um valor booleano.
       return $isCpfValid;					
   }

}
