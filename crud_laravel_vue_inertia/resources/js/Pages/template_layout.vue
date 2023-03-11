<script>
import {Head} from "@inertiajs/vue3";

export default{
  props: [
    "template_layout"
  ],
  components: {
    Head
  },
  data(){
    return{
      endereco_do_arquivo_css: "/css/" + this.template_layout.visual_escolhido + "/template_layout.css"
    }
  },
  mounted(){
    /* Ajustando altura do tronco para preencher a parte vertical visível da tela. */
    const body = document.getElementsByTagName("body")[0];
    const div_cabecalho_template = document.getElementById("div_cabecalho_template");
    const div_tronco_template = document.getElementById("div_tronco_template");
    
    const resize_observer = new ResizeObserver(function(){
      let altura_minima = window.innerHeight;
      
      var estilo_computado = window.getComputedStyle(body);
      altura_minima -= parseInt(estilo_computado.marginTop, 10);
      altura_minima -= parseInt(estilo_computado.borderTopWidth, 10);
      altura_minima -= parseInt(estilo_computado.paddingTop, 10);
      
      var estilo_computado = window.getComputedStyle(div_cabecalho_template);
      altura_minima -= parseInt(estilo_computado.marginTop, 10);
      altura_minima -= parseInt(estilo_computado.borderTopWidth, 10);
      altura_minima -= parseInt(estilo_computado.paddingTop, 10);
      altura_minima -= parseInt(estilo_computado.height, 10);
      altura_minima -= parseInt(estilo_computado.paddingBottom, 10);
      altura_minima -= parseInt(estilo_computado.borderBottomWidth, 10);
      altura_minima -= parseInt(estilo_computado.marginBottom, 10);
      
      var estilo_computado = window.getComputedStyle(div_tronco_template);
      altura_minima -= parseInt(estilo_computado.marginTop, 10);
      altura_minima -= parseInt(estilo_computado.borderTopWidth, 10);
      altura_minima -= parseInt(estilo_computado.paddingTop, 10);
      altura_minima -= parseInt(estilo_computado.paddingBottom, 10);
      altura_minima -= parseInt(estilo_computado.borderBottomWidth, 10);
      altura_minima -= parseInt(estilo_computado.marginBottom, 10);
      
      div_tronco_template.style.minHeight = altura_minima + "px";
    }.bind(this));
    resize_observer.observe(div_cabecalho_template);
  }
}
</script>

<template>
  <Head>
    <link :href="endereco_do_arquivo_css" type="text/css" rel="stylesheet"/>
  </Head>
  
  <div id="div_cabecalho_template">
    <h1 id="h1_titulo_do_sistema_template">
      <span>CRUD Laravel Vue Inertia</span>
    </h1>
    <nav id="nav_menu_do_sistema_template">
      <div id="div_opcoes_do_menu_do_sistema">
        <a id="link_inicio_template" class="opcao_do_menu_do_sistema" href="/inicio">Início</a>
        <a id="link_cadastrar_template" class="opcao_do_menu_do_sistema" href="/cadastrar_pessoa">Cadastrar</a>
        <a id="link_listar_template" class="opcao_do_menu_do_sistema" href="/listar_pessoas">Listar</a>
        <a id="link_tudo_em_um_template" class="opcao_do_menu_do_sistema" href="/tudo_em_um">Tudo em um</a>
      </div>
    </nav>
  </div>
  <div id="div_tronco_template">
    <slot name="conteudo"></slot>
  </div>
  <div id="div_rodape_template">
    <div id="div_autor_do_sistema_template">
      <span>Este sistema foi feito por Rodrigo Diniz da Silva.</span>
    </div>
    <div id="div_tecnologias_do_sistema_template">
      <span>Este sistema usa PHP, Laravel, Vue e Inertia.</span>
    </div>
  </div>
</template>
