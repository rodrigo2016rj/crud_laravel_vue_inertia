<script>
  import TemplateLayout from "../template_layout.vue";
  import {Head} from "@inertiajs/vue3";
  
  export default{
    props: [
      "template_layout",
      "excluir_pessoa"
    ],
    components: {
      TemplateLayout,
      Head
    },
    data(){
      return{
        endereco_do_arquivo_css: "/css/" + this.template_layout.visual_escolhido + "/excluir_pessoa.css"
      }
    },
    methods: {
      remover_foco_do_botao(evento){
        evento.currentTarget.blur();
      }
    }
  }
</script>

<template>
  <TemplateLayout :template_layout="template_layout">
    <template #conteudo>
      <div id="div_pagina_excluir_pessoa">
        <h2 id="h2_titulo_da_pagina">
          <span>Excluir Pessoa</span>
        </h2>
        <div v-if="excluir_pessoa.mensagem" id="div_mensagem">
          <span id="span_mensagem" :class="'mensagem_de_' + excluir_pessoa.tipo_de_mensagem">{{excluir_pessoa.mensagem}}</span>
        </div>
        <form v-if="excluir_pessoa.id_valido" id="form_confirmar_exclusao_de_pessoa" method="post" 
              action="/excluir_pessoa/excluir">
          <div id="div_texto_confirmar_exclusao_de_pessoa">
            <span>
              Tem certeza que você deseja excluir definitivamente, do banco de dados deste sistema, 
              {{excluir_pessoa.nome_completo_da_pessoa}}, CPF {{excluir_pessoa.cpf_da_pessoa}}, 
              cujo setor é o {{excluir_pessoa.setor_da_pessoa}}?
            </span>
          </div>
          <div id="div_botao_excluir">
            <input type="hidden" name="_token" :value="template_layout.chave_anti_csrf"/>
            <input type="hidden" id="campo_id_da_pessoa" name="id_da_pessoa" :value="excluir_pessoa.id_da_pessoa"/>
            <input type="submit" id="botao_excluir" @mouseleave="remover_foco_do_botao" @click="remover_foco_do_botao" 
                   value="Excluir Pessoa"/>
          </div>
        </form>
      </div>
    </template>
  </TemplateLayout>
  <Head>
    <title>Excluir Pessoa</title>
    <link :href="endereco_do_arquivo_css" type="text/css" rel="stylesheet"/>
  </Head>
</template>
