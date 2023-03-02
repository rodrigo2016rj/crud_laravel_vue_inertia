<script>
  import TemplateLayout from "../template_layout.vue";
  import {Head} from "@inertiajs/vue3";
  
  export default{
    props: [
      "template_layout"
    ],
    components: {
      TemplateLayout,
      Head
    },
    data(){
      return{
        endereco_do_arquivo_css: "/css/" + this.template_layout.visual_escolhido + "/inicio.css"
      }
    }
  }
</script>

<template>
  <TemplateLayout :template_layout="template_layout">
    <template #conteudo>
      <div id="div_pagina_inicio">
        <h2 id="h2_titulo_da_pagina">
          <span>Início</span>
        </h2>
        <div id="div_sobre_este_sistema">
          <h3 id="h3_titulo_sobre_este_sistema">
            <span>Sobre este sistema</span>
          </h3>
          <div id="div_conteudo_sobre_este_sistema">
            <p>
              Fiz este sistema para divulgar a minha forma de utilizar Laravel, Vue e Inertia. Este sistema 
              possui as quatro operações básicas: Create (INSERT), Read (SELECT), Update (UPDATE) e Delete 
              (DELETE), por isso o nome do sistema é CRUD Laravel Vue Inertia. No menu do sistema há 4 links:
            </p>
            <p>
              Início, onde o sistema mostra informações sobre si mesmo (esta página).
            </p>
            <p>
              Cadastrar, onde o sistema exibe um formulário para cadastrar pessoas.
            </p>
            <p>
              Listar, onde o sistema lista as pessoas cadastradas. Além das informações das pessoas, há 
              também na lista: link para a página das informações da pessoa, link para a página de editar 
              informações da pessoa e link para a página de excluir informações da pessoa.
            </p>
            <p>
              Tudo em um, onde o sistema exibe a lista das pessoas cadastradas e também possibilita 
              cadastrar, visualizar, editar e excluir informações das pessoas pela mesma página.
            </p>
          </div>
        </div>
      </div>
    </template>
  </TemplateLayout>
  <Head>
    <title>Início</title>
    <link :href="endereco_do_arquivo_css" type="text/css" rel="stylesheet"/>
  </Head>
</template>
