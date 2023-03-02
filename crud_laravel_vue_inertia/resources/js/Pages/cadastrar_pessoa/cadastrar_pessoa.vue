<script>
  import TemplateLayout from "../template_layout.vue";
  import SeletorDeData from "./seletor_de_data.vue";
  import {Head} from "@inertiajs/vue3";
  
  export default{
    props: [
      "template_layout",
      "cadastrar_pessoa"
    ],
    components: {
      TemplateLayout,
      SeletorDeData,
      Head
    },
    data(){
      return{
        endereco_do_arquivo_css: "/css/" + this.template_layout.visual_escolhido + "/cadastrar_pessoa.css",
        
        ultimo_valor_campo_cpf: "",
        mostrar_calendario_do_campo_data_de_nascimento: false,
        id_do_setor_da_descricao: null,
        posicao_x_da_descricao_do_setor: 0,
        posicao_y_da_descricao_do_setor: 0
      }
    },
    created(){
      /* Se o valor advindo do Laravel não for um dos valores possíveis, a caixa de seleção terá o valor default. */
      var valor_default_para_a_caixa_de_selecao = true;
      for(let i = 0; i < this.cadastrar_pessoa.setores.length; i++){
        if(this.cadastrar_pessoa.setores[i].id == this.cadastrar_pessoa.id_do_setor){
          valor_default_para_a_caixa_de_selecao = false;
          break;
        }
      }
      if(valor_default_para_a_caixa_de_selecao){
        this.cadastrar_pessoa.id_do_setor = "";
      }
      
      window.addEventListener("click", function(evento){
        let tag_alvo = evento.target;
        
        ciclo:
        while(true){
          if(tag_alvo === null || !tag_alvo.tagName){
            this.remover_popups();
            break;
          }
          
          switch(tag_alvo.id){
            case "campo_data_de_nascimento":
            case "span_icone_de_calendario_do_campo_data_de_nascimento":
            case "div_calendario":
            case "caixa_de_selecao_setor":
            case "div_descricoes_dos_setores":
              break ciclo;
            break;
          }
          
          tag_alvo = tag_alvo.parentNode;
        }
      }.bind(this));
    },
    methods: {
      aplicar_mascara_para_o_campo_cpf(evento){
        evento.preventDefault();
        
        const campo_cpf = evento.currentTarget;
        
        let posicao_do_cursor = campo_cpf.selectionStart;
        if(typeof posicao_do_cursor !== "number"){
          /* Se o navegador não suportar, cancela. */
          return;
        }
        
        const teclas_para_ignorar = [
          "Enter", "Tab", "Shift", "Control", "Backspace", "Home", "End", "Delete", 
          "Up", "Right", "Down", "Left", "ArrowUp", "ArrowRight", "ArrowDown", "ArrowLeft"
        ];
        if(teclas_para_ignorar.indexOf(evento.key) >= 0){
          this.ultimo_valor_campo_cpf = campo_cpf.value;
          return;
        }
        
        campo_cpf.value = campo_cpf.value.replace(/[^0-9]/g, "");
        
        if(campo_cpf.value.length >= 3){
          campo_cpf.value = campo_cpf.value.slice(0, 3) + "." + campo_cpf.value.slice(3);
          if((posicao_do_cursor === 3 || posicao_do_cursor === 4) && campo_cpf.value.length > this.ultimo_valor_campo_cpf.length){
            posicao_do_cursor++;
          }else if(campo_cpf.value.length >= 7 && posicao_do_cursor === 4){
            posicao_do_cursor++;
          }
        }
        if(campo_cpf.value.length >= 7){
          campo_cpf.value = campo_cpf.value.slice(0, 7) + "." + campo_cpf.value.slice(7);
          if((posicao_do_cursor === 7 || posicao_do_cursor === 8) && campo_cpf.value.length > this.ultimo_valor_campo_cpf.length){
            posicao_do_cursor++;
          }else if(campo_cpf.value.length >= 11 && posicao_do_cursor === 8){
            posicao_do_cursor++;
          }
        }
        if(campo_cpf.value.length >= 11){
          campo_cpf.value = campo_cpf.value.slice(0, 11) + "-" + campo_cpf.value.slice(11);
          if((posicao_do_cursor === 11 || posicao_do_cursor === 12) && campo_cpf.value.length > this.ultimo_valor_campo_cpf.length){
            posicao_do_cursor++;
          }
        }
        if(campo_cpf.value.length > 14){
          campo_cpf.value = campo_cpf.value.slice(0, 14);
        }
        
        campo_cpf.setSelectionRange(posicao_do_cursor, posicao_do_cursor);
        
        this.ultimo_valor_campo_cpf = campo_cpf.value;
        this.cadastrar_pessoa.cpf = campo_cpf.value;
      },
      colocar_borda_hover_no_campo(evento){
        let tag_alvo = evento.currentTarget;
        
        if(tag_alvo.id === "span_icone_de_calendario_do_campo_data_de_nascimento"){
          tag_alvo = tag_alvo.parentNode.querySelector("#campo_data_de_nascimento");
        }
        if(tag_alvo.id === "campo_data_de_nascimento"){
          tag_alvo.style.border = "1px solid #8080C8";
        }
      },
      colocar_borda_normal_no_campo(evento){
        let tag_alvo = evento.currentTarget;
        
        if(tag_alvo.id === "span_icone_de_calendario_do_campo_data_de_nascimento"){
          tag_alvo = tag_alvo.parentNode.querySelector("#campo_data_de_nascimento");
        }
        if(tag_alvo.id === "campo_data_de_nascimento"){
          tag_alvo.style.border = "1px solid #C8C8C8";
        }
      },
      desfaz_selecao_de_texto(evento){
        evento.preventDefault();
      },
      remover_foco_do_botao(evento){
        evento.currentTarget.blur();
      },
      aplicar_mascara_para_o_campo_telefone_fixo(evento){
        evento.preventDefault();
        
        const campo_telefone_fixo = evento.currentTarget;
        
        let posicao_do_cursor = campo_telefone_fixo.selectionStart;
        if(typeof posicao_do_cursor !== "number"){
          /* Se o navegador não suportar, cancela. */
          return;
        }
        
        const teclas_para_ignorar = [
          "Enter", "Tab", "Shift", "Control", "Backspace", "Home", "End", "Delete", 
          "Up", "Right", "Down", "Left", "ArrowUp", "ArrowRight", "ArrowDown", "ArrowLeft"
        ];
        if(teclas_para_ignorar.indexOf(evento.key) >= 0){
          return;
        }
        
        let atualizacao_do_cursor = 0;
        
        if(campo_telefone_fixo.value.charAt(0) !== "("){
          if(/[0-9]/.test(campo_telefone_fixo.value.charAt(0))){
            campo_telefone_fixo.value = "(" + campo_telefone_fixo.value;
            atualizacao_do_cursor++;
          }else{
            campo_telefone_fixo.value = "(" + campo_telefone_fixo.value.slice(1);
          }
        }
        if(/[^0-9]/.test(campo_telefone_fixo.value.charAt(1))){
          campo_telefone_fixo.value = campo_telefone_fixo.value.slice(0, 1) + campo_telefone_fixo.value.slice(2);
        }
        if(/[^0-9]/.test(campo_telefone_fixo.value.charAt(2))){
          campo_telefone_fixo.value = campo_telefone_fixo.value.slice(0, 2) + campo_telefone_fixo.value.slice(3);
        }
        if(campo_telefone_fixo.value.length > 3 && campo_telefone_fixo.value.charAt(3) !== ")"){
          if(/[0-9]/.test(campo_telefone_fixo.value.charAt(3))){
            campo_telefone_fixo.value = campo_telefone_fixo.value.slice(0, 3) + ")" + campo_telefone_fixo.value.slice(3);
            if(posicao_do_cursor >= 3){
              atualizacao_do_cursor++;
            }
          }else{
            campo_telefone_fixo.value = campo_telefone_fixo.value.slice(0, 3) + ")" + campo_telefone_fixo.value.slice(4);
          }
        }
        if(/[^0-9]/.test(campo_telefone_fixo.value.charAt(4))){
          campo_telefone_fixo.value = campo_telefone_fixo.value.slice(0, 4) + campo_telefone_fixo.value.slice(5);
        }
        if(/[^0-9]/.test(campo_telefone_fixo.value.charAt(5))){
          campo_telefone_fixo.value = campo_telefone_fixo.value.slice(0, 5) + campo_telefone_fixo.value.slice(6);
        }
        if(/[^0-9]/.test(campo_telefone_fixo.value.charAt(6))){
          campo_telefone_fixo.value = campo_telefone_fixo.value.slice(0, 6) + campo_telefone_fixo.value.slice(7);
        }
        if(/[^0-9]/.test(campo_telefone_fixo.value.charAt(7))){
          campo_telefone_fixo.value = campo_telefone_fixo.value.slice(0, 7) + campo_telefone_fixo.value.slice(8);
        }
        if(campo_telefone_fixo.value.length > 8 && campo_telefone_fixo.value.charAt(8) !== "-"){
          if(/[0-9]/.test(campo_telefone_fixo.value.charAt(8))){
            campo_telefone_fixo.value = campo_telefone_fixo.value.slice(0, 8) + "-" + campo_telefone_fixo.value.slice(8);
            if(posicao_do_cursor >= 8){
              atualizacao_do_cursor++;
            }
          }else{
            campo_telefone_fixo.value = campo_telefone_fixo.value.slice(0, 8) + "-" + campo_telefone_fixo.value.slice(9);
          }
        }
        if(/[^0-9]/.test(campo_telefone_fixo.value.charAt(9))){
          campo_telefone_fixo.value = campo_telefone_fixo.value.slice(0, 9) + campo_telefone_fixo.value.slice(10);
        }
        if(/[^0-9]/.test(campo_telefone_fixo.value.charAt(10))){
          campo_telefone_fixo.value = campo_telefone_fixo.value.slice(0, 10) + campo_telefone_fixo.value.slice(11);
        }
        if(/[^0-9]/.test(campo_telefone_fixo.value.charAt(11))){
          campo_telefone_fixo.value = campo_telefone_fixo.value.slice(0, 11) + campo_telefone_fixo.value.slice(12);
        }
        if(/[^0-9]/.test(campo_telefone_fixo.value.charAt(12))){
          campo_telefone_fixo.value = campo_telefone_fixo.value.slice(0, 12) + campo_telefone_fixo.value.slice(13);
        }
        if(campo_telefone_fixo.value.length > 13){
          campo_telefone_fixo.value = campo_telefone_fixo.value.slice(0, 13);
        }
        
        posicao_do_cursor += atualizacao_do_cursor;
        campo_telefone_fixo.setSelectionRange(posicao_do_cursor, posicao_do_cursor);
        
        this.cadastrar_pessoa.telefone_fixo = campo_telefone_fixo.value;
      },
      aplicar_mascara_para_o_campo_telefone_movel(evento){
        evento.preventDefault();
        
        const campo_telefone_movel = evento.currentTarget;
        
        let posicao_do_cursor = campo_telefone_movel.selectionStart;
        if(typeof posicao_do_cursor !== "number"){
          /* Se o navegador não suportar, cancela. */
          return;
        }
        
        const teclas_para_ignorar = [
          "Enter", "Tab", "Shift", "Control", "Backspace", "Home", "End", "Delete", 
          "Up", "Right", "Down", "Left", "ArrowUp", "ArrowRight", "ArrowDown", "ArrowLeft"
        ];
        if(teclas_para_ignorar.indexOf(evento.key) >= 0){
          return;
        }
        
        let atualizacao_do_cursor = 0;
        
        if(campo_telefone_movel.value.charAt(0) !== "("){
          if(/[0-9]/.test(campo_telefone_movel.value.charAt(0))){
            campo_telefone_movel.value = "(" + campo_telefone_movel.value;
            atualizacao_do_cursor++;
          }else{
            campo_telefone_movel.value = "(" + campo_telefone_movel.value.slice(1);
          }
        }
        if(/[^0-9]/.test(campo_telefone_movel.value.charAt(1))){
          campo_telefone_movel.value = campo_telefone_movel.value.slice(0, 1) + campo_telefone_movel.value.slice(2);
        }
        if(/[^0-9]/.test(campo_telefone_movel.value.charAt(2))){
          campo_telefone_movel.value = campo_telefone_movel.value.slice(0, 2) + campo_telefone_movel.value.slice(3);
        }
        if(campo_telefone_movel.value.length > 3 && campo_telefone_movel.value.charAt(3) !== ")"){
          if(/[0-9]/.test(campo_telefone_movel.value.charAt(3))){
            campo_telefone_movel.value = campo_telefone_movel.value.slice(0, 3) + ")" + campo_telefone_movel.value.slice(3);
            if(posicao_do_cursor >= 3){
              atualizacao_do_cursor++;
            }
          }else{
            campo_telefone_movel.value = campo_telefone_movel.value.slice(0, 3) + ")" + campo_telefone_movel.value.slice(4);
          }
        }
        if(/[^0-9]/.test(campo_telefone_movel.value.charAt(4))){
          campo_telefone_movel.value = campo_telefone_movel.value.slice(0, 4) + campo_telefone_movel.value.slice(5);
        }
        if(/[^0-9]/.test(campo_telefone_movel.value.charAt(5))){
          campo_telefone_movel.value = campo_telefone_movel.value.slice(0, 5) + campo_telefone_movel.value.slice(6);
        }
        if(/[^0-9]/.test(campo_telefone_movel.value.charAt(6))){
          campo_telefone_movel.value = campo_telefone_movel.value.slice(0, 6) + campo_telefone_movel.value.slice(7);
        }
        if(/[^0-9]/.test(campo_telefone_movel.value.charAt(7))){
          campo_telefone_movel.value = campo_telefone_movel.value.slice(0, 7) + campo_telefone_movel.value.slice(8);
        }
        if(/[^0-9]/.test(campo_telefone_movel.value.charAt(8))){
          campo_telefone_movel.value = campo_telefone_movel.value.slice(0, 8) + campo_telefone_movel.value.slice(9);
        }
        if(campo_telefone_movel.value.length > 9 && campo_telefone_movel.value.charAt(9) !== "-"){
          if(/[0-9]/.test(campo_telefone_movel.value.charAt(9))){
            campo_telefone_movel.value = campo_telefone_movel.value.slice(0, 9) + "-" + campo_telefone_movel.value.slice(9);
            if(posicao_do_cursor >= 9){
              atualizacao_do_cursor++;
            }
          }else{
            campo_telefone_movel.value = campo_telefone_movel.value.slice(0, 9) + "-" + campo_telefone_movel.value.slice(10);
          }
        }
        if(/[^0-9]/.test(campo_telefone_movel.value.charAt(10))){
          campo_telefone_movel.value = campo_telefone_movel.value.slice(0, 10) + campo_telefone_movel.value.slice(11);
        }
        if(/[^0-9]/.test(campo_telefone_movel.value.charAt(11))){
          campo_telefone_movel.value = campo_telefone_movel.value.slice(0, 11) + campo_telefone_movel.value.slice(12);
        }
        if(/[^0-9]/.test(campo_telefone_movel.value.charAt(12))){
          campo_telefone_movel.value = campo_telefone_movel.value.slice(0, 12) + campo_telefone_movel.value.slice(13);
        }
        if(/[^0-9]/.test(campo_telefone_movel.value.charAt(13))){
          campo_telefone_movel.value = campo_telefone_movel.value.slice(0, 13) + campo_telefone_movel.value.slice(14);
        }
        if(campo_telefone_movel.value.length > 14){
          campo_telefone_movel.value = campo_telefone_movel.value.slice(0, 14);
        }
        
        posicao_do_cursor += atualizacao_do_cursor;
        campo_telefone_movel.setSelectionRange(posicao_do_cursor, posicao_do_cursor);
        
        this.cadastrar_pessoa.telefone_movel = campo_telefone_movel.value;
      },
      remover_popups(){
        this.mostrar_calendario_do_campo_data_de_nascimento = false;
        this.id_do_setor_da_descricao = null;
      },
      colocar_popup(evento){
        /* Se já tiver o popup, irá remover. */
        switch(evento.currentTarget.getAttribute("id")){
          case "span_icone_de_calendario_do_campo_data_de_nascimento":
            if(this.mostrar_calendario_do_campo_data_de_nascimento){
              this.mostrar_calendario_do_campo_data_de_nascimento = false;
            }else{
              this.remover_popups();
              this.mostrar_calendario_do_campo_data_de_nascimento = true;
            }
          break;
          case "caixa_de_selecao_setor":
            const caixa_de_selecao_setor = evento.currentTarget;
            
            this.remover_popups();
            this.id_do_setor_da_descricao = caixa_de_selecao_setor.value;
            
            if(isNaN(this.id_do_setor_da_descricao) || this.id_do_setor_da_descricao % 1 != 0 || this.id_do_setor_da_descricao <= 0){
              return;
            }
            
            let posicao_x = caixa_de_selecao_setor.getBoundingClientRect().left + window.scrollX;
            let posicao_y = caixa_de_selecao_setor.getBoundingClientRect().top + window.scrollY;
            
            var estilo_computado = window.getComputedStyle(caixa_de_selecao_setor);
            posicao_x += parseInt(estilo_computado.width, 10);
            posicao_x += 10;
            
            posicao_y += parseInt(estilo_computado.height, 10) / 2;
            
            this.posicao_x_da_descricao_do_setor = posicao_x;
            this.posicao_y_da_descricao_do_setor = posicao_y;
          break;
        }
      }
    },
    watch: {
      id_do_setor_da_descricao(valor_atual, valor_anterior) {
        const descricoes_dos_setores = document.getElementsByClassName("descricao_do_setor");
        for(let i = 0; i < descricoes_dos_setores.length; i++){
          descricoes_dos_setores[i].classList.add("tag_oculta");
        }
        
        const div_descricoes_dos_setores = document.getElementById("div_descricoes_dos_setores");
        
        if(isNaN(valor_atual) || valor_atual % 1 != 0 || valor_atual <= 0){
          div_descricoes_dos_setores.classList.add("tag_oculta");
          return;
        }
        div_descricoes_dos_setores.classList.remove("tag_oculta");
        
        const div_descricao_do_setor = document.getElementById("div_descricao_do_setor_id_" + valor_atual);
        if(div_descricao_do_setor === null){
          div_descricoes_dos_setores.classList.add("tag_oculta");
          return;
        }
        div_descricao_do_setor.classList.remove("tag_oculta");
        
        var estilo_computado = window.getComputedStyle(div_descricoes_dos_setores);
        let recuo = 0;
        recuo -= parseInt(estilo_computado.borderTopWidth, 10) / 2;
        recuo -= parseInt(estilo_computado.paddingTop, 10) / 2;
        recuo -= parseInt(estilo_computado.height, 10) / 2;
        recuo -= parseInt(estilo_computado.paddingBottom, 10) / 2;
        recuo -= parseInt(estilo_computado.borderBottomWidth, 10) / 2;
        
        div_descricoes_dos_setores.style.position = "absolute";
        div_descricoes_dos_setores.style.top = this.posicao_y_da_descricao_do_setor + recuo + "px";
        div_descricoes_dos_setores.style.left = this.posicao_x_da_descricao_do_setor + "px";
      }
    }
  }
</script>

<template>
  <TemplateLayout :template_layout="template_layout">
    <template #conteudo>
      <div id="div_pagina_cadastrar_pessoa">
        <h2 id="h2_titulo_da_pagina">
          <span>Cadastrar Pessoa</span>
        </h2>
        <div id="div_mensagem" v-if="cadastrar_pessoa.mensagem">
          <span id="span_mensagem" :class="['mensagem_de_'+cadastrar_pessoa.tipo_de_mensagem]">{{cadastrar_pessoa.mensagem}}</span>
        </div>
        <form id="form_cadastrar" method="post" action="/cadastrar_pessoa/cadastrar">
          <div id="div_legenda_do_form_cadastrar">
            <span class="marcador_de_obrigatoriedade">* </span>
            <span>Indica que o campo é de preenchimento obrigatório.</span>
          </div>
          <div id="div_cadastrar_nome">
            <div id="div_label_nome">
              <label id="label_nome" for="campo_nome">
                <span class="marcador_de_obrigatoriedade">* </span>
                <span>Nome</span>
              </label>
            </div>
            <div id="div_campo_nome">
              <input type="text" id="campo_nome" name="nome" v-model="cadastrar_pessoa.nome" autocomplete="off"/>
            </div>
          </div>
          <div id="div_cadastrar_sobrenome">
            <div id="div_label_sobrenome">
              <label id="label_sobrenome" for="campo_sobrenome">
                <span class="marcador_de_obrigatoriedade">* </span>
                <span>Sobrenome</span>
              </label>
            </div>
            <div id="div_campo_sobrenome">
              <input type="text" id="campo_sobrenome" name="sobrenome" v-model="cadastrar_pessoa.sobrenome" 
                     autocomplete="off"/>
            </div>
          </div>
          <div id="div_cadastrar_cpf">
            <div id="div_label_cpf">
              <label id="label_cpf" for="campo_cpf">
                <span class="marcador_de_obrigatoriedade">* </span>
                <span>CPF</span>
              </label>
            </div>
            <div id="div_campo_cpf">
              <input type="text" id="campo_cpf" name="cpf" @keyup="aplicar_mascara_para_o_campo_cpf" 
                     v-model="cadastrar_pessoa.cpf" autocomplete="off"/>
            </div>
          </div>
          <div id="div_cadastrar_data_de_nascimento">
            <div id="div_label_data_de_nascimento">
              <label id="label_data_de_nascimento" for="campo_data_de_nascimento">
                <span class="marcador_de_obrigatoriedade">* </span>
                <span>Data de nascimento</span>
              </label>
            </div>
            <div id="div_campo_data_de_nascimento">
              <input type="text" id="campo_data_de_nascimento" name="data_de_nascimento" 
                     v-model="cadastrar_pessoa.data_de_nascimento" @mouseenter="colocar_borda_hover_no_campo" 
                     @mouseleave="colocar_borda_normal_no_campo" autocomplete="off"/>
              <span id="span_icone_de_calendario_do_campo_data_de_nascimento" @mouseenter="colocar_borda_hover_no_campo" 
                    @mouseleave="colocar_borda_normal_no_campo" @mousedown="desfaz_selecao_de_texto" 
                    @click="colocar_popup"></span>
              <SeletorDeData v-if="mostrar_calendario_do_campo_data_de_nascimento"
                             id_do_campo="campo_data_de_nascimento"></SeletorDeData>
            </div>
          </div>
          <div id="div_cadastrar_sexo">
            <div id="div_label_lista_de_sexos">
              <label id="label_lista_de_sexos">
                <span class="marcador_de_obrigatoriedade">* </span>
                <span>Sexo</span>
              </label>
            </div>
            <div id="div_lista_de_sexos">
              <template v-for="(valor, chave) in cadastrar_pessoa.sexos">
                <label class="item_da_lista_de_sexos">
                  <input type="radio" name="sexo" :value="chave" v-model="cadastrar_pessoa.sexo" autocomplete="off"/>
                  <span>&nbsp;</span>
                  <span>{{valor}}</span>
                </label>
              </template>
            </div>
          </div>
          <div id="div_cadastrar_setor">
            <div id="div_label_setor">
              <label id="label_setor" for="caixa_de_selecao_setor">
                <span class="marcador_de_obrigatoriedade">* </span>
                <span>Setor</span>
              </label>
            </div>
            <div id="div_caixa_de_selecao_setor">
              <select id="caixa_de_selecao_setor" name="id_do_setor" v-model="cadastrar_pessoa.id_do_setor" 
                      @change="colocar_popup" autocomplete="off">
                <option value="">Selecione</option>
                <option v-for="(setor, chave) in cadastrar_pessoa.setores" :value="setor.id">{{setor.nome}}</option>
              </select>
              <div id="div_descricoes_dos_setores" class="tag_oculta">
                <template v-for="(setor, chave) in cadastrar_pessoa.setores">
                  <div :id="'div_descricao_do_setor_id_'+setor.id" class="descricao_do_setor tag_oculta">
                    <span>{{setor.descricao}}</span>
                  </div>
                </template>
              </div>
            </div>
          </div>
          <div id="div_cadastrar_email">
            <div id="div_label_email">
              <label id="label_email" for="campo_email">
                <span class="marcador_de_obrigatoriedade">* </span>
                <span>E-mail</span>
              </label>
            </div>
            <div id="div_campo_email">
              <input type="text" id="campo_email" name="email" v-model="cadastrar_pessoa.email" autocomplete="off"/>
            </div>
          </div>
          <div id="div_cadastrar_telefone_fixo">
            <div id="div_label_telefone_fixo">
              <label id="label_telefone_fixo" for="campo_telefone_fixo">
                <span>Número do telefone fixo</span>
              </label>
            </div>
            <div id="div_campo_telefone_fixo">
              <input type="text" id="campo_telefone_fixo" name="telefone_fixo" v-model="cadastrar_pessoa.telefone_fixo" 
                     @keyup="aplicar_mascara_para_o_campo_telefone_fixo" autocomplete="off"/>
            </div>
          </div>
          <div id="div_cadastrar_telefone_movel">
            <div id="div_label_telefone_movel">
              <label id="label_telefone_movel" for="campo_telefone_movel">
                <span>Número de celular</span>
              </label>
            </div>
            <div id="div_campo_telefone_movel">
              <input type="text" id="campo_telefone_movel" name="telefone_movel" v-model="cadastrar_pessoa.telefone_movel" 
                     @keyup="aplicar_mascara_para_o_campo_telefone_movel" autocomplete="off"/>
            </div>
          </div>
          <div id="div_cadastrar_telefone_estrangeiro">
            <div id="div_label_telefone_estrangeiro">
              <label id="label_telefone_estrangeiro" for="campo_telefone_estrangeiro">
                <span>Número para contato no exterior</span>
              </label>
            </div>
            <div id="div_campo_telefone_estrangeiro">
              <input type="text" id="campo_telefone_estrangeiro" name="telefone_estrangeiro" 
                     v-model="cadastrar_pessoa.telefone_estrangeiro" autocomplete="off"/>
            </div>
          </div>
          <div id="div_botao_cadastrar">
            <input type="hidden" name="_token" :value="template_layout.chave_anti_csrf"/>
            <input type="submit" id="botao_cadastrar" @mouseleave="remover_foco_do_botao" 
                   @click="remover_foco_do_botao" value="Cadastrar"/>
          </div>
        </form>
      </div>
    </template>
  </TemplateLayout>
  <Head>
    <title>Cadastrar Pessoa</title>
    <link :href="endereco_do_arquivo_css" type="text/css" rel="stylesheet"/>
  </Head>
</template>
