<?php

namespace App\Http\Controllers;

use App\Models\PessoaModel;
use DateTimeZone;
use DateTime;
use Inertia\Inertia;

final class PessoaController extends TemplateLayoutController{

  public function carregar_pagina(){
    $valores = $this->valores_do_template_layout();
    $pessoa_model = new PessoaModel();

    /* Colocando valores iniciais nas variáveis para não ficarem undefined no Vue */
    $valores['pessoa']['mensagem'] = '';
    $valores['pessoa']['tipo_de_mensagem'] = 'sucesso';
    $valores['pessoa']['id_valido'] = true;
    $valores['pessoa']['id_da_pessoa'] = '';
    $valores['pessoa']['nome_completo_da_pessoa'] = '';
    $valores['pessoa']['cpf_da_pessoa'] = '';
    $valores['pessoa']['data_de_nascimento_da_pessoa'] = '';
    $valores['pessoa']['idade_da_pessoa'] = '';
    $valores['pessoa']['sexo_da_pessoa'] = '';
    $valores['pessoa']['sexo_classe_css'] = '';
    $valores['pessoa']['setor_da_pessoa'] = '';
    $valores['pessoa']['email_da_pessoa'] = '';
    $valores['pessoa']['telefone_fixo_da_pessoa'] = '';
    $valores['pessoa']['telefone_movel_da_pessoa'] = '';
    $valores['pessoa']['telefone_estrangeiro_da_pessoa'] = '';

    /* Validando o ID da pessoa informado na URL */
    $requisicao = $this->get_requisicao();
    $id_da_pessoa = $requisicao->get('id');
    if(!is_numeric($id_da_pessoa) or $id_da_pessoa <= 0 or floor($id_da_pessoa) != $id_da_pessoa){
      $mensagem = 'ID inválido, o ID da pessoa precisa ser um número natural maior que zero.';
      $valores['pessoa']['mensagem'] = $mensagem;
      $valores['pessoa']['tipo_de_mensagem'] = 'falha';
      $valores['pessoa']['id_valido'] = false;
    }else{
      /* Consultando e mostrando informações da pessoa */
      $array_resultado = $pessoa_model->selecionar_pessoa($id_da_pessoa);
      if(isset($array_resultado['mensagem_do_model'])){
        $valores['pessoa']['mensagem'] = $array_resultado['mensagem_do_model'];
        $valores['pessoa']['tipo_de_mensagem'] = 'falha';
        $valores['pessoa']['id_valido'] = false;
      }else{
        $pessoa = $array_resultado[0];

        $id = $pessoa->get_pk_pessoa();
        $valores['pessoa']['id_da_pessoa'] = $id;

        $nome_completo = $pessoa->nome_completo();
        $valores['pessoa']['nome_completo_da_pessoa'] = $nome_completo;

        $cpf = $pessoa->get_cpf();
        $valores['pessoa']['cpf_da_pessoa'] = $cpf;

        $data_de_nascimento = $pessoa->get_data_de_nascimento();
        $data_convertida = $this->converter_para_data_do_html($data_de_nascimento);
        $valores['pessoa']['data_de_nascimento_da_pessoa'] = $data_convertida;

        $sem_fuso_horario = new DateTimeZone('GMT');
        $momento_atual = new DateTime('now', $sem_fuso_horario);
        $data_de_nascimento = new DateTime($data_de_nascimento, $sem_fuso_horario);
        $tempo_decorrido = date_diff($momento_atual, $data_de_nascimento);
        $idade = $tempo_decorrido->y;
        $valores['pessoa']['idade_da_pessoa'] = $idade;

        $sexo = $pessoa->get_sexo();
        $array_sexos = $pessoa->enum_sexo();
        $sexo = $array_sexos[$sexo];
        $valores['pessoa']['sexo_da_pessoa'] = $sexo;

        $sexo_classe_css = '';
        switch($sexo){
          case 'Masculino':
            $sexo_classe_css = 'azul_claro';
            break;
          case 'Feminino':
            $sexo_classe_css = 'rosa';
            break;
        }
        $valores['pessoa']['sexo_classe_css'] = $sexo_classe_css;

        $nome_do_setor = $pessoa->get_setor()->get_nome();
        $nome_do_setor = str_replace('Setor ', '', $nome_do_setor);
        $valores['pessoa']['setor_da_pessoa'] = $nome_do_setor;

        $email = $pessoa->get_email();
        $valores['pessoa']['email_da_pessoa'] = $email;

        $telefone_fixo = $pessoa->get_telefone_fixo();
        $valores['pessoa']['telefone_fixo_da_pessoa'] = $telefone_fixo;

        $telefone_movel = $pessoa->get_telefone_movel();
        $valores['pessoa']['telefone_movel_da_pessoa'] = $telefone_movel;

        $telefone_estrangeiro = $pessoa->get_telefone_estrangeiro();
        $valores['pessoa']['telefone_estrangeiro_da_pessoa'] = $telefone_estrangeiro;
      }
    }

    return Inertia::render('pessoa/pessoa', $valores);
  }

}
