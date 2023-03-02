<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class TemplateLayoutController extends Controller{
  /* Armazena o objeto da requisição. */
  private $requisicao;

  public function __construct(Request $requisicao){
    $this->requisicao = $requisicao;
  }

  protected final function get_requisicao(){
    return $this->requisicao;
  }

  /** ---------------------------------------------------------------------------------------------
    Retorna valores que podem ser usados em várias páginas deste sistema */
  protected final function valores_do_template_layout(){
    $valores = array();

    $valores['template_layout']['visual_escolhido'] = 'visual_padrao';
    $valores['template_layout']['chave_anti_csrf'] = csrf_token();

    return $valores;
  }

  /** ---------------------------------------------------------------------------------------------
    Converte dd/MM/yyyy para: yyyy-MM-dd */
  protected function converter_para_data_do_sql($data){
    if(!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data)){
      /* Caso não venha no formato certo, retorna a string sem conversão. */
      return $data;
    }
    $dia = substr($data, 0, 2);
    $mes = substr($data, 3, 2);
    $ano = substr($data, 6, 4);
    return "$ano-$mes-$dia";
  }

  /** ---------------------------------------------------------------------------------------------
    Converte yyyy-MM-dd para: dd/MM/yyyy */
  protected function converter_para_data_do_html($data){
    if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)){
      /* Caso não venha no formato certo, retorna a string sem conversão. */
      return $data;
    }
    $ano = substr($data, 0, 4);
    $mes = substr($data, 5, 2);
    $dia = substr($data, 8, 2);
    return "$dia/$mes/$ano";
  }

}
