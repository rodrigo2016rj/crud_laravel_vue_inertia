<script>
  import TemplateLayout from "../template_layout.vue";
  import {Head} from "@inertiajs/vue3";
  
  export default{
    props: [
      "template_layout",
      "pessoa"
    ],
    components: {
      TemplateLayout,
      Head
    },
    data(){
      return{
        /* Propriedades obtidas dos controllers precisam ser recolocadas para prevenir o cache do inertia */
        vue_template_layout: this.template_layout,
        vue_pessoa: this.pessoa,
        
        /* Propriedades novas e seus valores iniciais */
        endereco_do_arquivo_css: "/css/" + this.template_layout.visual_escolhido + "/pessoa.css"
      }
    }
  }
</script>

<template>
  <TemplateLayout :template_layout="vue_template_layout">
    <template #conteudo>
      <div id="div_pagina_pessoa">
        <h2 id="h2_titulo_da_pagina">
          <span v-if="vue_pessoa.id_valido">{{vue_pessoa.nome_completo_da_pessoa}}</span>
          <span v-else>Pessoa</span>
        </h2>
        <div v-if="vue_pessoa.mensagem" id="div_mensagem">
          <span id="span_mensagem" :class="'mensagem_de_'+vue_pessoa.tipo_de_mensagem">{{vue_pessoa.mensagem}}</span>
        </div>
        <template v-if="vue_pessoa.id_valido">
          <div id="div_opcoes_da_pagina">
            <h3 id="h3_titulo_das_opcoes_da_pagina">
              <span>Opções</span>
            </h3>
            <div id="div_lista_de_opcoes_da_pagina">
              <a id="link_editar_pessoa" class="opcao_da_pagina" 
                 :href="'/editar_pessoa?id='+vue_pessoa.id_da_pessoa">Editar Pessoa</a>
              <a id="link_excluir_pessoa" class="opcao_da_pagina" 
                 :href="'/excluir_pessoa?id='+vue_pessoa.id_da_pessoa">Excluir Pessoa</a>
            </div>
          </div>
          <div id="div_local_das_informacoes_da_pessoa">
            <h3 id="h3_titulo_das_informacoes_da_pessoa">
              <span>Informações</span>
            </h3>
            <div id="div_informacoes_da_pessoa">
              <div id="div_local_do_nome_completo" class="informacao_da_pessoa">
                <span class="parte_generica_da_informacao">Nome: </span>
                <div id="div_nome_completo" class="local_da_parte_especifica_da_informacao">
                  <span id="span_nome_completo" 
                        class="parte_especifica_da_informacao">{{vue_pessoa.nome_completo_da_pessoa}}</span>
                </div>
              </div>
              <div id="div_local_do_cpf" class="informacao_da_pessoa">
                <span class="parte_generica_da_informacao">CPF: </span>
                <div id="div_cpf" class="local_da_parte_especifica_da_informacao">
                  <span id="span_cpf" class="parte_especifica_da_informacao">{{vue_pessoa.cpf_da_pessoa}}</span>
                </div>
              </div>
              <div id="div_local_da_data_de_nascimento" class="informacao_da_pessoa">
                <span class="parte_generica_da_informacao">Data de nascimento: </span>
                <div id="div_data_de_nascimento" class="local_da_parte_especifica_da_informacao">
                  <span id="span_data_de_nascimento" class="parte_especifica_da_informacao">{{vue_pessoa.data_de_nascimento_da_pessoa}}</span>
                </div>
              </div>
              <div id="div_local_da_idade" class="informacao_da_pessoa">
                <span class="parte_generica_da_informacao">Idade: </span>
                <div id="div_idade" class="local_da_parte_especifica_da_informacao">
                  <span id="span_idade" class="parte_especifica_da_informacao">{{vue_pessoa.idade_da_pessoa}}</span>
                </div>
              </div>
              <div id="div_local_do_sexo" class="informacao_da_pessoa">
                <span class="parte_generica_da_informacao">Sexo: </span>
                <div id="div_sexo" class="local_da_parte_especifica_da_informacao">
                  <span id="span_sexo" :class="['parte_especifica_da_informacao', vue_pessoa.sexo_classe_css]">{{vue_pessoa.sexo_da_pessoa}}</span>
                </div>
              </div>
              <div id="div_local_do_setor" class="informacao_da_pessoa">
                <span class="parte_generica_da_informacao">Setor: </span>
                <div id="div_setor" class="local_da_parte_especifica_da_informacao">
                  <span id="span_setor" class="parte_especifica_da_informacao">{{vue_pessoa.setor_da_pessoa}}</span>
                </div>
              </div>
              <div id="div_local_do_email" class="informacao_da_pessoa">
                <span class="parte_generica_da_informacao">E-mail: </span>
                <div id="div_email" class="local_da_parte_especifica_da_informacao">
                  <span id="span_email" class="parte_especifica_da_informacao">{{vue_pessoa.email_da_pessoa}}</span>
                </div>
              </div>
              <div id="div_local_do_telefone_fixo" class="informacao_da_pessoa">
                <span class="parte_generica_da_informacao">Número do telefone fixo: </span>
                <div v-if="vue_pessoa.telefone_fixo_da_pessoa" id="div_telefone_fixo" 
                     class="local_da_parte_especifica_da_informacao">
                  <span id="span_telefone_fixo" class="parte_especifica_da_informacao">{{vue_pessoa.telefone_fixo_da_pessoa}}</span>
                </div>
                <div v-else id="div_telefone_fixo" class="local_da_parte_especifica_da_informacao">
                  <span class="sem_informacao">Não informado</span>
                </div>
              </div>
              <div id="div_local_do_telefone_movel" class="informacao_da_pessoa">
                <span class="parte_generica_da_informacao">Número de celular: </span>
                <div v-if="vue_pessoa.telefone_movel_da_pessoa" id="div_telefone_movel" 
                     class="local_da_parte_especifica_da_informacao">
                  <span id="span_telefone_movel" class="parte_especifica_da_informacao">{{vue_pessoa.telefone_movel_da_pessoa}}</span>
                </div>
                <div v-else id="div_telefone_movel" class="local_da_parte_especifica_da_informacao">
                  <span class="sem_informacao">Não informado</span>
                </div>
              </div>
              <div id="div_local_do_telefone_estrangeiro" class="informacao_da_pessoa">
                <span class="parte_generica_da_informacao">Número para contato no exterior: </span>
                <div v-if="vue_pessoa.telefone_estrangeiro_da_pessoa" id="div_telefone_estrangeiro" 
                     class="local_da_parte_especifica_da_informacao">
                  <span id="span_telefone_estrangeiro" class="parte_especifica_da_informacao">{{vue_pessoa.telefone_estrangeiro_da_pessoa}}</span>
                </div>
                <div v-else id="div_telefone_estrangeiro" class="local_da_parte_especifica_da_informacao">
                  <span class="sem_informacao">Não informado</span>
                </div>
              </div>
            </div>
          </div>
        </template>
      </div>
    </template>
  </TemplateLayout>
  <Head>
    <title>Pessoa</title>
    <link :href="endereco_do_arquivo_css" type="text/css" rel="stylesheet"/>
  </Head>
</template>
