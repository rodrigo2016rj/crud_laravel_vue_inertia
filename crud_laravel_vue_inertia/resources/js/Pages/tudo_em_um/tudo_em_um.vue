<script>
  import TemplateLayout from "../template_layout.vue";
  import SeletorDeData from "./seletor_de_data.vue";
  import {Head} from "@inertiajs/vue3";
  
  export default{
    props: [
      "template_layout",
      "tudo_em_um"
    ],
    components: {
      TemplateLayout,
      SeletorDeData,
      Head
    },
    data(){
      return{
        /* Propriedades obtidas dos controllers precisam ser recolocadas para prevenir o cache do inertia */
        vue_template_layout: this.template_layout,
        vue_tudo_em_um: this.tudo_em_um,
        
        /* Propriedades novas e seus valores iniciais */
        endereco_do_arquivo_css: "/css/" + this.template_layout.visual_escolhido + "/tudo_em_um.css",
        
        pagina_selecionada: this.tudo_em_um.lista_de_pessoas.pagina_atual,
        manter_popup: null,
        
        posicao_y_do_link_cadastrar_pessoa: 0,
        mostrar_popup_cadastrar_pessoa: false,
        cadastrar_nome: "",
        cadastrar_sobrenome: "",
        cadastrar_cpf: "",
        ultimo_valor_do_campo_cadastrar_cpf: "",
        cadastrar_data_de_nascimento: "",
        mostrar_calendario_do_campo_cadastrar_data_de_nascimento: false,
        cadastrar_sexo: "",
        cadastrar_setor: "",
        cadastrar_email: "",
        cadastrar_telefone_fixo: "",
        cadastrar_telefone_movel: "",
        cadastrar_telefone_estrangeiro: "",
        mensagem_cadastrar_pessoa: "",
        tipo_da_mensagem_cadastrar_pessoa: null,
        
        filtro_nome: this.tudo_em_um.lista_de_pessoas.filtro_nome,
        filtro_cpf: this.tudo_em_um.lista_de_pessoas.filtro_cpf,
        filtro_data_de_nascimento: this.tudo_em_um.lista_de_pessoas.filtro_data_de_nascimento,
        filtro_id_do_setor: this.tudo_em_um.lista_de_pessoas.filtro_id_do_setor,
        quantidade_por_pagina: this.tudo_em_um.lista_de_pessoas.quantidade_por_pagina,
        ordenacao: this.tudo_em_um.lista_de_pessoas.ordenacao,
        
        ultimo_valor_do_campo_filtro_cpf: "",
        mostrar_calendario_do_campo_filtro_data_de_nascimento: false,
        status_da_busca: "",
        contador_ajax: 0,
        
        lista_de_paginas: new Array(0),
        
        posicao_y_do_link_nome_da_pessoa: 0,
        id_da_pessoa_do_popup_visualizar: null,
        
        posicao_y_do_link_editar_pessoa: 0,
        id_da_pessoa_do_popup_editar: null,
        editar_nome: "",
        editar_sobrenome: "",
        editar_cpf: "",
        ultimo_valor_do_campo_editar_cpf: "",
        editar_data_de_nascimento: "",
        mostrar_calendario_do_campo_editar_data_de_nascimento: false,
        editar_sexo: "",
        editar_setor: "",
        editar_email: "",
        editar_telefone_fixo: "",
        editar_telefone_movel: "",
        editar_telefone_estrangeiro: "",
        mensagem_editar_pessoa: "",
        tipo_da_mensagem_editar_pessoa: null,
        
        posicao_y_do_link_excluir_pessoa: 0,
        id_da_pessoa_do_popup_excluir: null,
        nome_completo_da_pessoa_que_sera_excluida: "",
        cpf_da_pessoa_que_sera_excluida: "",
        nome_do_setor_da_pessoa_que_sera_excluida: "",
        mensagem_excluir_pessoa: "",
        tipo_da_mensagem_excluir_pessoa: null,
        
        rolagem_horizontal_da_janela: 0,
        rolagem_vertical_da_janela: 0
      }
    },
    created(){
      /* Se o valor advindo do Laravel não for um dos valores possíveis, a caixa de seleção terá o valor default. */
      var valor_default_para_a_caixa_de_selecao = true;
      for(let i = 0; i < this.vue_tudo_em_um.setores.length; i++){
        if(this.vue_tudo_em_um.setores[i].id == this.vue_tudo_em_um.lista_de_pessoas.filtro_id_do_setor){
          valor_default_para_a_caixa_de_selecao = false;
          break;
        }
      }
      if(valor_default_para_a_caixa_de_selecao){
        this.filtro_id_do_setor = "";
      }
      
      if(Object.keys(this.vue_tudo_em_um.quantidades_por_pagina).indexOf(this.vue_tudo_em_um.lista_de_pessoas.quantidade_por_pagina.toString()) === -1){
        this.quantidade_por_pagina = "padrao";
      }
      
      this.criar_lista_de_paginas();
      
      window.addEventListener("click", function(evento){
        let tag_alvo = evento.target;
        
        ciclo:
        while(true){
          if(tag_alvo === null || !tag_alvo.tagName){
            this.remover_popups();
            break;
          }
          
          switch(tag_alvo.id){
            case "link_cadastrar_pessoa":
            case "div_cadastrar_pessoa":
            case "campo_filtro_data_de_nascimento":
            case "span_icone_de_calendario_do_campo_filtro_data_de_nascimento":
            case "div_editar_pessoa":
            case "div_excluir_pessoa":
            case "div_calendario":
              break ciclo;
            break;
          }
          
          if(tag_alvo.classList.contains("nome_da_pessoa")){
            break;
          }
          if(tag_alvo.classList.contains("div_visualizar_pessoa")){
            break;
          }
          if(tag_alvo.classList.contains("link_editar_pessoa")){
            break;
          }
          if(tag_alvo.classList.contains("link_excluir_pessoa")){
            break;
          }
          
          tag_alvo = tag_alvo.parentNode;
        }
      }.bind(this));
      
      window.addEventListener("resize", function(evento){
        this.rolagem_horizontal_da_janela = window.scrollX;
        this.rolagem_vertical_da_janela = window.scrollY;
        if(this.mostrar_popup_cadastrar_pessoa === true){
          this.mostrar_popup_cadastrar_pessoa = "reposicionar";
        }
        if(!isNaN(this.id_da_pessoa_do_popup_visualizar) 
           && this.id_da_pessoa_do_popup_visualizar % 1 == 0 
           && this.id_da_pessoa_do_popup_visualizar > 0){
          this.id_da_pessoa_do_popup_visualizar = "reposicionar";
        }
        if(!isNaN(this.id_da_pessoa_do_popup_editar) 
           && this.id_da_pessoa_do_popup_editar % 1 == 0 
           && this.id_da_pessoa_do_popup_editar > 0){
          this.id_da_pessoa_do_popup_editar = "reposicionar";
        }
        if(!isNaN(this.id_da_pessoa_do_popup_excluir) 
           && this.id_da_pessoa_do_popup_excluir % 1 == 0 
           && this.id_da_pessoa_do_popup_excluir > 0){
          this.id_da_pessoa_do_popup_excluir = "reposicionar";
        }
      }.bind(this));
      
      window.addEventListener("scroll", function(evento){
        this.rolagem_horizontal_da_janela = window.scrollX;
        this.rolagem_vertical_da_janela = window.scrollY;
      }.bind(this));
    },
    methods: {
      aplicar_mascara_de_cpf(evento, ultimo_valor_do_campo_cpf){
        const campo_cpf = evento.currentTarget;
        
        let posicao_do_cursor = campo_cpf.selectionStart;
        if(typeof posicao_do_cursor !== "number"){
          /* Se o navegador não suportar, cancela. */
          return campo_cpf.value;
        }
        
        const teclas_para_ignorar = [
          "Enter", "Tab", "Shift", "Control", "Backspace", "Home", "End", "Delete", 
          "Up", "Right", "Down", "Left", "ArrowUp", "ArrowRight", "ArrowDown", "ArrowLeft"
        ];
        if(teclas_para_ignorar.indexOf(evento.key) >= 0){
          return campo_cpf.value;
        }
        
        campo_cpf.value = campo_cpf.value.replace(/[^0-9]/g, "");
        
        if(campo_cpf.value.length >= 3){
          campo_cpf.value = campo_cpf.value.slice(0, 3) + "." + campo_cpf.value.slice(3);
          if((posicao_do_cursor === 3 || posicao_do_cursor === 4) && campo_cpf.value.length > ultimo_valor_do_campo_cpf.length){
            posicao_do_cursor++;
          }else if(campo_cpf.value.length >= 7 && posicao_do_cursor === 4){
            posicao_do_cursor++;
          }
        }
        if(campo_cpf.value.length >= 7){
          campo_cpf.value = campo_cpf.value.slice(0, 7) + "." + campo_cpf.value.slice(7);
          if((posicao_do_cursor === 7 || posicao_do_cursor === 8) && campo_cpf.value.length > ultimo_valor_do_campo_cpf.length){
            posicao_do_cursor++;
          }else if(campo_cpf.value.length >= 11 && posicao_do_cursor === 8){
            posicao_do_cursor++;
          }
        }
        if(campo_cpf.value.length >= 11){
          campo_cpf.value = campo_cpf.value.slice(0, 11) + "-" + campo_cpf.value.slice(11);
          if((posicao_do_cursor === 11 || posicao_do_cursor === 12) && campo_cpf.value.length > ultimo_valor_do_campo_cpf.length){
            posicao_do_cursor++;
          }
        }
        if(campo_cpf.value.length > 14){
          campo_cpf.value = campo_cpf.value.slice(0, 14);
        }
        
        campo_cpf.setSelectionRange(posicao_do_cursor, posicao_do_cursor);
        
        return campo_cpf.value;
      },
      aplicar_mascara_de_telefone_fixo(evento){
        const campo_telefone_fixo = evento.currentTarget;
        
        let posicao_do_cursor = campo_telefone_fixo.selectionStart;
        if(typeof posicao_do_cursor !== "number"){
          /* Se o navegador não suportar, cancela. */
          return campo_telefone_fixo.value;
        }
        
        const teclas_para_ignorar = [
          "Enter", "Tab", "Shift", "Control", "Backspace", "Home", "End", "Delete", 
          "Up", "Right", "Down", "Left", "ArrowUp", "ArrowRight", "ArrowDown", "ArrowLeft"
        ];
        if(teclas_para_ignorar.indexOf(evento.key) >= 0){
          return campo_telefone_fixo.value;
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
        
        return campo_telefone_fixo.value;
      },
      aplicar_mascara_de_telefone_movel(evento){
        const campo_telefone_movel = evento.currentTarget;
        
        let posicao_do_cursor = campo_telefone_movel.selectionStart;
        if(typeof posicao_do_cursor !== "number"){
          /* Se o navegador não suportar, cancela. */
          return campo_telefone_movel.value;
        }
        
        const teclas_para_ignorar = [
          "Enter", "Tab", "Shift", "Control", "Backspace", "Home", "End", "Delete", 
          "Up", "Right", "Down", "Left", "ArrowUp", "ArrowRight", "ArrowDown", "ArrowLeft"
        ];
        if(teclas_para_ignorar.indexOf(evento.key) >= 0){
          return campo_telefone_movel.value;
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
        
        return campo_telefone_movel.value;
      },
      aplicar_mascara_para_o_campo_cadastrar_cpf(evento){
        evento.preventDefault();
        
        const novo_valor = this.aplicar_mascara_de_cpf(evento, this.ultimo_valor_do_campo_cadastrar_cpf);
        
        this.ultimo_valor_do_campo_cadastrar_cpf = novo_valor;
        this.cadastrar_cpf = novo_valor;
      },
      colocar_borda_hover_no_campo_cadastrar_data_de_nascimento(evento){
        let tag_alvo = evento.currentTarget;
        
        if(tag_alvo.id === "span_icone_de_calendario_do_campo_cadastrar_data_de_nascimento"){
          tag_alvo = tag_alvo.parentNode.querySelector("#campo_cadastrar_data_de_nascimento");
        }
        if(tag_alvo.id === "campo_cadastrar_data_de_nascimento"){
          tag_alvo.style.border = "1px solid #8080C8";
        }
      },
      colocar_borda_normal_no_campo_cadastrar_data_de_nascimento(evento){
        let tag_alvo = evento.currentTarget;
        
        if(tag_alvo.id === "span_icone_de_calendario_do_campo_cadastrar_data_de_nascimento"){
          tag_alvo = tag_alvo.parentNode.querySelector("#campo_cadastrar_data_de_nascimento");
        }
        if(tag_alvo.id === "campo_cadastrar_data_de_nascimento"){
          tag_alvo.style.border = "1px solid #C8C8C8";
        }
      },
      desfaz_selecao_de_texto(evento){
        evento.preventDefault();
      },
      aplicar_mascara_para_o_campo_cadastrar_telefone_fixo(evento){
        evento.preventDefault();
        
        const novo_valor = this.aplicar_mascara_de_telefone_fixo(evento);
        
        this.cadastrar_telefone_fixo = novo_valor;
      },
      aplicar_mascara_para_o_campo_cadastrar_telefone_movel(evento){
        evento.preventDefault();
        
        const novo_valor = this.aplicar_mascara_de_telefone_movel(evento);
        
        this.cadastrar_telefone_movel = novo_valor;
      },
      remover_foco_do_botao(evento){
        evento.currentTarget.blur();
      },
      cadastrar(evento){
        this.remover_foco_do_botao(evento);
        
        this.contador_ajax++;
        this.status_da_busca = "Atualizando...";
        
        this.mensagem_cadastrar_pessoa = "Aguarde...";
        this.tipo_da_mensagem_cadastrar_pessoa = null;
        
        const numero_desta_acao_ajax = this.contador_ajax;
        
        /* Requisição ajax */
        let conexao_ajax = null;
        if(window.XMLHttpRequest){
          conexao_ajax = new XMLHttpRequest();
        }else if(window.ActiveXObject){
          conexao_ajax = new ActiveXObject("Microsoft.XMLHTTP");
        }
        const tipo = "POST";
        let url_mais = "";
        let url = "/tudo_em_um/cadastrar_pessoa_ajax" + url_mais;
        let dados_post = {filtro_nome: "", filtro_cpf: "", filtro_data_de_nascimento: "", filtro_id_do_setor: "",
                          quantidade_por_pagina: "", ordenacao: null, pagina: 1, nome: this.cadastrar_nome, 
                          sobrenome: this.cadastrar_sobrenome, cpf: this.cadastrar_cpf, 
                          data_de_nascimento: this.cadastrar_data_de_nascimento, sexo: this.cadastrar_sexo,
                          id_do_setor: this.cadastrar_setor, email: this.cadastrar_email, 
                          telefone_fixo: this.cadastrar_telefone_fixo, telefone_movel: this.cadastrar_telefone_movel, 
                          telefone_estrangeiro: this.cadastrar_telefone_estrangeiro};
        let resposta = null;
        conexao_ajax.onreadystatechange = function(){
          if(conexao_ajax.readyState == 4){
            if(conexao_ajax.status == 200){
              resposta = JSON.parse(conexao_ajax.responseText);
              if(numero_desta_acao_ajax >= this.contador_ajax){
                if(typeof resposta.mensagem_de_falha != "undefined"){
                  this.mensagem_cadastrar_pessoa = resposta.mensagem_de_falha;
                  this.tipo_da_mensagem_cadastrar_pessoa = "mensagem_de_falha";
                }
                if(typeof resposta.mensagem_de_sucesso != "undefined"){
                  this.mensagem_cadastrar_pessoa = resposta.mensagem_de_sucesso;
                  this.tipo_da_mensagem_cadastrar_pessoa = "mensagem_de_sucesso";
                  
                  this.cadastrar_nome = "";
                  this.cadastrar_sobrenome = "";
                  this.cadastrar_cpf = "";
                  this.cadastrar_data_de_nascimento = "";
                  this.cadastrar_sexo = "";
                  this.cadastrar_setor = "";
                  this.cadastrar_email = "";
                  this.cadastrar_telefone_fixo = "";
                  this.cadastrar_telefone_movel = "";
                  this.cadastrar_telefone_estrangeiro = "";
                  
                  this.filtro_nome = "";
                  this.filtro_cpf = "";
                  this.filtro_data_de_nascimento = "";
                  this.filtro_id_do_setor = "";
                  this.quantidade_por_pagina = "padrao";
                  this.ordenacao = "padrao";
                  
                  this.vue_tudo_em_um.lista_de_pessoas = resposta.lista_de_pessoas;
                  this.vue_tudo_em_um.sexos = resposta.sexos;
                  this.vue_tudo_em_um.setores = resposta.setores;
                  
                  this.pagina_selecionada = 1;
                  
                  this.criar_lista_de_paginas();
                }
                
                this.status_da_busca = "";
              }
            }
          }
        }.bind(this);
        conexao_ajax.open(tipo, url, true);
        conexao_ajax.setRequestHeader("Content-Type", "application/json");
        conexao_ajax.setRequestHeader("X-CSRF-TOKEN", this.vue_template_layout.chave_anti_csrf);
        conexao_ajax.send(JSON.stringify(dados_post));
      },
      criar_lista_de_paginas(){
        const pagina_atual = this.vue_tudo_em_um.lista_de_pessoas.pagina_atual;
        const inicio_da_sequencia = Math.max(pagina_atual - 3, 1);
        const final_da_sequencia = Math.min(pagina_atual + 3, this.vue_tudo_em_um.lista_de_pessoas.ultima_pagina);
        const lista_de_paginas = new Array(0);
        
        if(inicio_da_sequencia != 1){
          lista_de_paginas.push("...");
        }
        
        for(var pagina=inicio_da_sequencia; pagina <= final_da_sequencia; pagina++){
          lista_de_paginas.push(pagina);
        }
        
        if(final_da_sequencia != this.vue_tudo_em_um.lista_de_pessoas.ultima_pagina){
          lista_de_paginas.push("...");
        }
        
        this.lista_de_paginas = lista_de_paginas;
      },
      filtrar_por_nome(evento){
        this.filtro_nome = evento.currentTarget.value;
        this.filtrar();
      },
      filtrar_por_cpf(evento){
        evento.preventDefault();
        
        const novo_valor = this.aplicar_mascara_de_cpf(evento, this.ultimo_valor_do_campo_filtro_cpf);
        
        this.ultimo_valor_do_campo_filtro_cpf = novo_valor;
        this.filtro_cpf = novo_valor;
        
        this.filtrar();
      },
      filtrar_por_data_de_nascimento(evento){
        this.filtro_data_de_nascimento = evento.currentTarget.value;
        this.filtrar();
      },
      colocar_borda_hover_no_campo_filtro_data_de_nascimento(evento){
        let tag_alvo = evento.currentTarget;
        
        if(tag_alvo.id === "span_icone_de_calendario_do_campo_filtro_data_de_nascimento"){
          tag_alvo = tag_alvo.parentNode.querySelector("#campo_filtro_data_de_nascimento");
        }
        if(tag_alvo.id === "campo_filtro_data_de_nascimento"){
          tag_alvo.style.border = "1px solid #8080C8";
        }
      },
      colocar_borda_normal_no_campo_filtro_data_de_nascimento(evento){
        let tag_alvo = evento.currentTarget;
        
        if(tag_alvo.id === "span_icone_de_calendario_do_campo_filtro_data_de_nascimento"){
          tag_alvo = tag_alvo.parentNode.querySelector("#campo_filtro_data_de_nascimento");
        }
        if(tag_alvo.id === "campo_filtro_data_de_nascimento"){
          tag_alvo.style.border = "1px solid #C8C8C8";
        }
      },
      filtrar_por_setor(evento){
        this.filtro_id_do_setor = evento.currentTarget.value;
        this.filtrar();
      },
      mudar_quantidade_por_pagina(evento){
        this.quantidade_por_pagina = evento.currentTarget.value;
        this.filtrar();
      },
      buscar(evento){
        this.remover_foco_do_botao(evento);
        this.filtrar();
      },
      filtrar(){
        this.contador_ajax++;
        this.status_da_busca = "Buscando...";
        this.pagina_selecionada = 1;
        
        this.enviar_formulario_de_filtro();
      },
      limpar(evento){
        this.remover_foco_do_botao(evento);
        
        this.contador_ajax++;
        this.status_da_busca = "Limpando...";
        this.filtro_nome = "";
        this.filtro_cpf = "";
        this.filtro_data_de_nascimento = "";
        this.filtro_id_do_setor = "";
        this.quantidade_por_pagina = "padrao";
        this.ordenacao = "padrao";
        this.pagina_selecionada = 1;
        
        this.enviar_formulario_de_filtro();
      },
      mudar_pagina(evento){
        evento.preventDefault();
        
        const href = evento.currentTarget.getAttribute("href");
        const pagina = href.replace("/tudo_em_um?pagina=", "");
        
        this.contador_ajax++;
        this.status_da_busca = "Mudando de página...";
        this.pagina_selecionada = pagina;
        
        this.enviar_formulario_de_filtro();
      },
      ordenar_por_nome_completo(evento){
        this.contador_ajax++;
        this.status_da_busca = "Ordenando...";
        this.pagina_selecionada = 1;
        switch(this.ordenacao){
          case "nome_completo_a_z":
            this.ordenacao = "nome_completo_z_a";
            break;
          case "nome_completo_z_a":
            this.ordenacao = "padrao";
            break;
          default:
            this.ordenacao = "nome_completo_a_z";
            break;
        }
        this.enviar_formulario_de_filtro();
      },
      ordenar_por_cpf(evento){
        this.contador_ajax++;
        this.status_da_busca = "Ordenando...";
        this.pagina_selecionada = 1;
        switch(this.ordenacao){
          case "cpf_crescente":
            this.ordenacao = "cpf_decrescente";
            break;
          case "cpf_decrescente":
            this.ordenacao = "padrao";
            break;
          default:
            this.ordenacao = "cpf_crescente";
            break;
        }
        this.enviar_formulario_de_filtro();
      },
      ordenar_por_setor(evento){
        this.contador_ajax++;
        this.status_da_busca = "Ordenando...";
        this.pagina_selecionada = 1;
        switch(this.ordenacao){
          case "setor_a_z":
            this.ordenacao = "setor_z_a";
            break;
          case "setor_z_a":
            this.ordenacao = "padrao";
            break;
          default:
            this.ordenacao = "setor_a_z";
            break;
        }
        this.enviar_formulario_de_filtro();
      },
      ordenar_por_contato(evento){
        this.contador_ajax++;
        this.status_da_busca = "Ordenando...";
        this.pagina_selecionada = 1;
        switch(this.ordenacao){
          case "contato_a_z":
            this.ordenacao = "contato_z_a";
            break;
          case "contato_z_a":
            this.ordenacao = "padrao";
            break;
          default:
            this.ordenacao = "contato_a_z";
            break;
        }
        this.enviar_formulario_de_filtro();
      },
      enviar_formulario_de_filtro(){
        const numero_desta_acao_ajax = this.contador_ajax;
        const filtro_nome = this.filtro_nome;
        const filtro_cpf = this.filtro_cpf;
        const filtro_data_de_nascimento = this.filtro_data_de_nascimento;
        const filtro_id_do_setor = this.filtro_id_do_setor;
        const quantidade_por_pagina = this.quantidade_por_pagina;
        const ordenacao = this.ordenacao;
        const pagina = this.pagina_selecionada;
        
        /* Requisição ajax */
        let conexao_ajax = null;
        if(window.XMLHttpRequest){
          conexao_ajax = new XMLHttpRequest();
        }else if(window.ActiveXObject){
          conexao_ajax = new ActiveXObject("Microsoft.XMLHTTP");
        }
        const tipo = "GET";
        let url_mais = "";
        url_mais += "?filtro_nome=" + filtro_nome;
        url_mais += "&filtro_cpf=" + filtro_cpf;
        url_mais += "&filtro_data_de_nascimento=" + filtro_data_de_nascimento;
        url_mais += "&filtro_id_do_setor=" + filtro_id_do_setor;
        url_mais += "&quantidade_por_pagina=" + quantidade_por_pagina;
        url_mais += "&pagina=" + pagina;
        url_mais += "&ordenacao=" + ordenacao;
        let url = "/tudo_em_um/mostrar_pessoas_ajax" + url_mais;
        let dados_post = null;
        let resposta = null;
        conexao_ajax.onreadystatechange = function(){
          if(conexao_ajax.readyState == 4){
            if(conexao_ajax.status == 200){
              if(numero_desta_acao_ajax >= this.contador_ajax){
                resposta = JSON.parse(conexao_ajax.responseText);
                
                this.vue_tudo_em_um.lista_de_pessoas = resposta.lista_de_pessoas;
                this.vue_tudo_em_um.sexos = resposta.sexos;
                this.vue_tudo_em_um.setores = resposta.setores;
                
                this.status_da_busca = "";
                
                this.criar_lista_de_paginas();
              }
            }
          }
        }.bind(this);
        conexao_ajax.open(tipo, url, true);
        conexao_ajax.setRequestHeader("Content-Type", "application/json");
        conexao_ajax.setRequestHeader("X-CSRF-TOKEN", this.vue_template_layout.chave_anti_csrf);
        conexao_ajax.send(JSON.stringify(dados_post));
      },
      aplicar_mascara_para_o_campo_editar_cpf(evento){
        evento.preventDefault();
        
        const novo_valor = this.aplicar_mascara_de_cpf(evento, this.ultimo_valor_do_campo_editar_cpf);
        
        this.ultimo_valor_do_campo_editar_cpf = novo_valor;
        this.editar_cpf = novo_valor;
      },
      colocar_borda_hover_no_campo_editar_data_de_nascimento(evento){
        let tag_alvo = evento.currentTarget;
        
        if(tag_alvo.id === "span_icone_de_calendario_do_campo_editar_data_de_nascimento"){
          tag_alvo = tag_alvo.parentNode.querySelector("#campo_editar_data_de_nascimento");
        }
        if(tag_alvo.id === "campo_editar_data_de_nascimento"){
          tag_alvo.style.border = "1px solid #8080C8";
        }
      },
      colocar_borda_normal_no_campo_editar_data_de_nascimento(evento){
        let tag_alvo = evento.currentTarget;
        
        if(tag_alvo.id === "span_icone_de_calendario_do_campo_editar_data_de_nascimento"){
          tag_alvo = tag_alvo.parentNode.querySelector("#campo_editar_data_de_nascimento");
        }
        if(tag_alvo.id === "campo_editar_data_de_nascimento"){
          tag_alvo.style.border = "1px solid #C8C8C8";
        }
      },
      aplicar_mascara_para_o_campo_editar_telefone_fixo(evento){
        evento.preventDefault();
        
        const novo_valor = this.aplicar_mascara_de_telefone_fixo(evento);
        
        this.editar_telefone_fixo = novo_valor;
      },
      aplicar_mascara_para_o_campo_editar_telefone_movel(evento){
        evento.preventDefault();
        
        const novo_valor = this.aplicar_mascara_de_telefone_movel(evento);
        
        this.editar_telefone_movel = novo_valor;
      },
      editar(evento){
        this.remover_foco_do_botao(evento);
        
        this.contador_ajax++;
        this.status_da_busca = "Atualizando...";
        
        this.mensagem_editar_pessoa = "Aguarde...";
        this.tipo_da_mensagem_editar_pessoa = null;
        
        const numero_desta_acao_ajax = this.contador_ajax;
        const filtro_nome = this.filtro_nome;
        const filtro_cpf = this.filtro_cpf;
        const filtro_data_de_nascimento = this.filtro_data_de_nascimento;
        const filtro_id_do_setor = this.filtro_id_do_setor;
        const quantidade_por_pagina = this.quantidade_por_pagina;
        const ordenacao = this.ordenacao;
        const pagina = this.pagina_selecionada;
        
        const id_da_pessoa = this.id_da_pessoa_do_popup_editar;
        
        /* Requisição ajax */
        let conexao_ajax = null;
        if(window.XMLHttpRequest){
          conexao_ajax = new XMLHttpRequest();
        }else if(window.ActiveXObject){
          conexao_ajax = new ActiveXObject("Microsoft.XMLHTTP");
        }
        const tipo = "POST";
        let url_mais = "";
        let url = "/tudo_em_um/editar_pessoa_ajax" + url_mais;
        let dados_post = {filtro_nome: filtro_nome, filtro_cpf: filtro_cpf, 
                          filtro_data_de_nascimento: filtro_data_de_nascimento, filtro_id_do_setor: filtro_id_do_setor, 
                          quantidade_por_pagina: quantidade_por_pagina, pagina: pagina, ordenacao: ordenacao, 
                          nome: this.editar_nome, sobrenome: this.editar_sobrenome, cpf: this.editar_cpf, 
                          data_de_nascimento: this.editar_data_de_nascimento, sexo: this.editar_sexo,
                          id_do_setor: this.editar_setor, email: this.editar_email, 
                          telefone_fixo: this.editar_telefone_fixo, telefone_movel: this.editar_telefone_movel, 
                          telefone_estrangeiro: this.editar_telefone_estrangeiro, id_da_pessoa: id_da_pessoa};
        let resposta = null;
        conexao_ajax.onreadystatechange = function(){
          if(conexao_ajax.readyState == 4){
            if(conexao_ajax.status == 200){
              resposta = JSON.parse(conexao_ajax.responseText);
              if(numero_desta_acao_ajax >= this.contador_ajax){
                if(typeof resposta.mensagem_de_falha != "undefined"){
                  if(id_da_pessoa === this.id_da_pessoa_do_popup_editar){
                    this.mensagem_editar_pessoa = resposta.mensagem_de_falha;
                    this.tipo_da_mensagem_editar_pessoa = "mensagem_de_falha";
                  }
                }
                
                if(typeof resposta.mensagem_de_sucesso != "undefined"){
                  if(id_da_pessoa === this.id_da_pessoa_do_popup_editar){
                    this.mensagem_editar_pessoa = resposta.mensagem_de_sucesso;
                    this.tipo_da_mensagem_editar_pessoa = "mensagem_de_sucesso";
                  }
                  
                  this.vue_tudo_em_um.lista_de_pessoas = resposta.lista_de_pessoas;
                  this.vue_tudo_em_um.sexos = resposta.sexos;
                  this.vue_tudo_em_um.setores = resposta.setores;
                  
                  this.criar_lista_de_paginas();
                }
                
                this.status_da_busca = "";
              }
            }
          }
        }.bind(this);
        conexao_ajax.open(tipo, url, true);
        conexao_ajax.setRequestHeader("Content-Type", "application/json");
        conexao_ajax.setRequestHeader("X-CSRF-TOKEN", this.vue_template_layout.chave_anti_csrf);
        conexao_ajax.send(JSON.stringify(dados_post));
      },
      excluir(evento){
        this.remover_foco_do_botao(evento);
        
        this.contador_ajax++;
        this.status_da_busca = "Atualizando...";
        
        this.mensagem_excluir_pessoa = "Aguarde...";
        this.tipo_da_mensagem_excluir_pessoa = null;
        
        const numero_desta_acao_ajax = this.contador_ajax;
        const filtro_nome = this.filtro_nome;
        const filtro_cpf = this.filtro_cpf;
        const filtro_data_de_nascimento = this.filtro_data_de_nascimento;
        const filtro_id_do_setor = this.filtro_id_do_setor;
        const quantidade_por_pagina = this.quantidade_por_pagina;
        const ordenacao = this.ordenacao;
        const pagina = this.pagina_selecionada;
        
        const id_da_pessoa = this.id_da_pessoa_do_popup_excluir;
        
        /* Requisição ajax */
        let conexao_ajax = null;
        if(window.XMLHttpRequest){
          conexao_ajax = new XMLHttpRequest();
        }else if(window.ActiveXObject){
          conexao_ajax = new ActiveXObject("Microsoft.XMLHTTP");
        }
        const tipo = "POST";
        let url_mais = "";
        let url = "/tudo_em_um/excluir_pessoa_ajax" + url_mais;
        let dados_post = {filtro_nome: filtro_nome, filtro_cpf: filtro_cpf, 
                          filtro_data_de_nascimento: filtro_data_de_nascimento, filtro_id_do_setor: filtro_id_do_setor, 
                          quantidade_por_pagina: quantidade_por_pagina, pagina: pagina, ordenacao: ordenacao, 
                          id_da_pessoa: id_da_pessoa};
        let resposta = null;
        conexao_ajax.onreadystatechange = function(){
          if(conexao_ajax.readyState == 4){
            if(conexao_ajax.status == 200){
              resposta = JSON.parse(conexao_ajax.responseText);
              if(numero_desta_acao_ajax >= this.contador_ajax){
                if(typeof resposta.mensagem_de_falha != "undefined"){
                  if(id_da_pessoa === this.id_da_pessoa_do_popup_excluir){
                    this.mensagem_excluir_pessoa = resposta.mensagem_de_falha;
                    this.tipo_da_mensagem_excluir_pessoa = "mensagem_de_falha";
                  }
                }
                
                if(typeof resposta.mensagem_de_sucesso != "undefined"){
                  if(id_da_pessoa === this.id_da_pessoa_do_popup_excluir){
                    this.mensagem_excluir_pessoa = resposta.mensagem_de_sucesso;
                    this.tipo_da_mensagem_excluir_pessoa = "mensagem_de_sucesso";
                  }
                  
                  this.vue_tudo_em_um.lista_de_pessoas = resposta.lista_de_pessoas;
                  this.vue_tudo_em_um.sexos = resposta.sexos;
                  this.vue_tudo_em_um.setores = resposta.setores;
                  
                  this.criar_lista_de_paginas();
                }
                
                this.status_da_busca = "";
              }
            }
          }
        }.bind(this);
        conexao_ajax.open(tipo, url, true);
        conexao_ajax.setRequestHeader("Content-Type", "application/json");
        conexao_ajax.setRequestHeader("X-CSRF-TOKEN", this.vue_template_layout.chave_anti_csrf);
        conexao_ajax.send(JSON.stringify(dados_post));
      },
      remover_popups(){
        if(this.manter_popup !== "popup_cadastrar_pessoa"){
          this.mostrar_popup_cadastrar_pessoa = false;
        }
        this.mostrar_calendario_do_campo_cadastrar_data_de_nascimento = false;
        
        this.mostrar_calendario_do_campo_filtro_data_de_nascimento = false;
        
        this.id_da_pessoa_do_popup_visualizar = null;
        
        if(this.manter_popup !== "popup_editar_pessoa"){
          this.id_da_pessoa_do_popup_editar = null;
        }
        this.mostrar_calendario_do_campo_editar_data_de_nascimento = false;
        
        this.id_da_pessoa_do_popup_excluir = null;
        
        this.manter_popup = null;
      },
      exibir_popup_cadastrar_pessoa(evento){
        evento.preventDefault();
        
        this.posicao_y_do_link_cadastrar_pessoa = evento.currentTarget.getBoundingClientRect().top;
        
        if(this.mostrar_popup_cadastrar_pessoa === true){
          this.remover_popups();
          this.mostrar_popup_cadastrar_pessoa = "reposicionar";
        }else{
          this.remover_popups();
          this.mostrar_popup_cadastrar_pessoa = true;
        }
      },
      manter_ou_remover_popups_da_div_cadastrar_pessoa(evento){
        let tag_alvo = evento.target;
        
        ciclo:
        while(true){
          if(tag_alvo.id === "div_cadastrar_pessoa"){
            this.manter_popup = "popup_cadastrar_pessoa";
            this.remover_popups();
            break;
          }
          
          switch(tag_alvo.id){
            case "campo_cadastrar_data_de_nascimento":
            case "span_icone_de_calendario_do_campo_cadastrar_data_de_nascimento":
            case "div_calendario":
              break ciclo;
            break;
          }
          
          tag_alvo = tag_alvo.parentNode;
        }
      },
      colocar_ou_remover_popup_calendario_do_campo_cadastrar_data_de_nascimento(evento){
        /* Se já tiver o popup, irá remover. */
        if(this.mostrar_calendario_do_campo_cadastrar_data_de_nascimento){
          this.mostrar_calendario_do_campo_cadastrar_data_de_nascimento = false;
        }else{
          this.manter_popup = "popup_cadastrar_pessoa";
          this.remover_popups();
          this.mostrar_calendario_do_campo_cadastrar_data_de_nascimento = true;
        }
      },
      colocar_ou_remover_popup_calendario_do_campo_filtro_data_de_nascimento(evento){
        /* Se já tiver o popup, irá remover. */
        if(this.mostrar_calendario_do_campo_filtro_data_de_nascimento){
          this.mostrar_calendario_do_campo_filtro_data_de_nascimento = false;
        }else{
          this.remover_popups();
          this.mostrar_calendario_do_campo_filtro_data_de_nascimento = true;
        }
      },
      colocar_popup_visualizar_pessoa(evento){
        evento.preventDefault();
        
        const tag_alvo = evento.currentTarget;
        
        this.posicao_y_do_link_nome_da_pessoa = tag_alvo.getBoundingClientRect().top;
        
        const id_da_pessoa = parseInt(tag_alvo.getAttribute("href").replace("pessoa?id=", ""), 10);
        
        if(this.id_da_pessoa_do_popup_visualizar === id_da_pessoa){
          this.remover_popups();
          this.id_da_pessoa_do_popup_visualizar = "reposicionar";
        }else{
          this.remover_popups();
          this.id_da_pessoa_do_popup_visualizar = id_da_pessoa;
        }
      },
      exibir_popup_editar_pessoa(nome, sobrenome, cpf, data_de_nascimento, sexo, id_do_setor, email, telefone_fixo, 
                                 telefone_movel, telefone_estrangeiro, id, evento){
        evento.preventDefault();
        
        const tag_alvo = evento.currentTarget;
        const id_da_pessoa = parseInt(tag_alvo.getAttribute("href").replace("editar_pessoa?id=", ""), 10);
        
        if(id_da_pessoa !== id){
          return;
        }
        
        this.editar_nome = nome;
        this.editar_sobrenome = sobrenome;
        this.editar_cpf = cpf;
        this.editar_data_de_nascimento = data_de_nascimento;
        this.editar_sexo = sexo;
        this.editar_setor = id_do_setor;
        this.editar_email = email;
        this.editar_telefone_fixo = telefone_fixo;
        this.editar_telefone_movel = telefone_movel;
        this.editar_telefone_estrangeiro = telefone_estrangeiro;
        
        this.posicao_y_do_link_editar_pessoa = tag_alvo.getBoundingClientRect().top;
        
        if(this.id_da_pessoa_do_popup_editar === id_da_pessoa){
          this.remover_popups();
          this.id_da_pessoa_do_popup_editar = "reposicionar";
        }else{
          this.remover_popups();
          this.id_da_pessoa_do_popup_editar = id_da_pessoa;
        }
      },
      manter_ou_remover_popups_da_div_editar_pessoa(evento){
        let tag_alvo = evento.target;
        
        ciclo:
        while(true){
          if(tag_alvo.id === "div_editar_pessoa"){
            this.manter_popup = "popup_editar_pessoa";
            this.remover_popups();
            break;
          }
          
          switch(tag_alvo.id){
            case "campo_editar_data_de_nascimento":
            case "span_icone_de_calendario_do_campo_editar_data_de_nascimento":
            case "div_calendario":
              break ciclo;
            break;
          }
          
          tag_alvo = tag_alvo.parentNode;
        }
      },
      colocar_ou_remover_popup_calendario_do_campo_editar_data_de_nascimento(evento){
        /* Se já tiver o popup, irá remover. */
        if(this.mostrar_calendario_do_campo_editar_data_de_nascimento){
          this.mostrar_calendario_do_campo_editar_data_de_nascimento = false;
        }else{
          this.manter_popup = "popup_editar_pessoa";
          this.remover_popups();
          this.mostrar_calendario_do_campo_editar_data_de_nascimento = true;
        }
      },
      exibir_popup_excluir_pessoa(nome_completo, cpf, nome_do_setor, id, evento){
        evento.preventDefault();
        
        const tag_alvo = evento.currentTarget;
        const id_da_pessoa = parseInt(tag_alvo.getAttribute("href").replace("excluir_pessoa?id=", ""), 10);
        
        if(id_da_pessoa !== id){
          return;
        }
        
        this.nome_completo_da_pessoa_que_sera_excluida = nome_completo;
        this.cpf_da_pessoa_que_sera_excluida = cpf;
        this.nome_do_setor_da_pessoa_que_sera_excluida = nome_do_setor;
        
        this.posicao_y_do_link_excluir_pessoa = tag_alvo.getBoundingClientRect().top;
        
        if(this.id_da_pessoa_do_popup_excluir === id_da_pessoa){
          this.remover_popups();
          this.id_da_pessoa_do_popup_excluir = "reposicionar";
        }else{
          this.remover_popups();
          this.id_da_pessoa_do_popup_excluir = id_da_pessoa;
        }
      }
    },
    watch: {
      mostrar_popup_cadastrar_pessoa(valor_atual, valor_anterior){
        if(valor_atual === "reposicionar"){
          /* Aciona o watch novamente */
          this.mostrar_popup_cadastrar_pessoa = true;
          return;
        }
        
        const div_cadastrar_pessoa = document.getElementById("div_cadastrar_pessoa");
        if(valor_atual === true){
          div_cadastrar_pessoa.classList.remove("tag_oculta");
          
          this.mensagem_cadastrar_pessoa = "";
          this.tipo_da_mensagem_cadastrar_pessoa = null;
          
          let largura_da_div = 0;
          var estilo_computado = window.getComputedStyle(div_cadastrar_pessoa);
          largura_da_div += parseInt(estilo_computado.borderLeftWidth, 10);
          largura_da_div += parseInt(estilo_computado.paddingLeft, 10);
          largura_da_div += parseInt(estilo_computado.width, 10);
          largura_da_div += parseInt(estilo_computado.paddingRight, 10);
          largura_da_div += parseInt(estilo_computado.borderRightWidth, 10);
          
          const tag_html = document.querySelector("html");
          
          let largura_da_tag_html = 0;
          var estilo_computado = window.getComputedStyle(tag_html);
          largura_da_tag_html += parseInt(estilo_computado.width, 10);
          
          var posicao_x = largura_da_tag_html / 2 - largura_da_div / 2;
          if(window.innerWidth <= largura_da_div){
            posicao_x = 0;
          }
          
          let altura_da_div = 0;
          var estilo_computado = window.getComputedStyle(div_cadastrar_pessoa);
          altura_da_div += parseInt(estilo_computado.borderTopWidth, 10);
          altura_da_div += parseInt(estilo_computado.paddingTop, 10);
          altura_da_div += parseInt(estilo_computado.height, 10);
          altura_da_div += parseInt(estilo_computado.paddingBottom, 10);
          altura_da_div += parseInt(estilo_computado.borderBottomWidth, 10);
          
          var posicao_y = this.rolagem_vertical_da_janela + (window.innerHeight - altura_da_div) / 2;
          if(window.innerHeight <= altura_da_div){
            posicao_y = this.rolagem_vertical_da_janela + 2;
            if(window.innerWidth <= largura_da_div){
              posicao_y = this.posicao_y_do_link_cadastrar_pessoa + this.rolagem_vertical_da_janela;
            }
          }
          
          div_cadastrar_pessoa.style.position = "absolute";
          div_cadastrar_pessoa.style.top = posicao_y + "px";
          div_cadastrar_pessoa.style.left = posicao_x + "px";
        }else{
          div_cadastrar_pessoa.classList.add("tag_oculta");
        }
      },
      id_da_pessoa_do_popup_visualizar(valor_atual, valor_anterior) {
        const div_visualizar_pessoa_anterior = document.getElementById("div_visualizar_pessoa_do_id_" + valor_anterior);
        if(div_visualizar_pessoa_anterior !== null){
          div_visualizar_pessoa_anterior.classList.add("tag_oculta");
        }
        
        if(valor_atual === null){
          return;
        }else if(valor_atual === "reposicionar"){
          /* Aciona o watch novamente */
          valor_atual = valor_anterior;
          this.id_da_pessoa_do_popup_visualizar = valor_atual;
          return;
        }
        
        const div_visualizar_pessoa = document.getElementById("div_visualizar_pessoa_do_id_" + valor_atual);
        div_visualizar_pessoa.classList.remove("tag_oculta");
        
        let largura_da_div = 0;
        var estilo_computado = window.getComputedStyle(div_visualizar_pessoa);
        largura_da_div += parseInt(estilo_computado.borderLeftWidth, 10);
        largura_da_div += parseInt(estilo_computado.paddingLeft, 10);
        largura_da_div += parseInt(estilo_computado.width, 10);
        largura_da_div += parseInt(estilo_computado.paddingRight, 10);
        largura_da_div += parseInt(estilo_computado.borderRightWidth, 10);
        
        const tag_html = document.querySelector("html");
        
        let largura_da_tag_html = 0;
        var estilo_computado = window.getComputedStyle(tag_html);
        largura_da_tag_html += parseInt(estilo_computado.width, 10);
        
        var posicao_x = largura_da_tag_html / 2 - largura_da_div / 2;
        if(window.innerWidth <= largura_da_div){
          posicao_x = 0;
        }
        
        let altura_da_div = 0;
        var estilo_computado = window.getComputedStyle(div_visualizar_pessoa);
        altura_da_div += parseInt(estilo_computado.borderTopWidth, 10);
        altura_da_div += parseInt(estilo_computado.paddingTop, 10);
        altura_da_div += parseInt(estilo_computado.height, 10);
        altura_da_div += parseInt(estilo_computado.paddingBottom, 10);
        altura_da_div += parseInt(estilo_computado.borderBottomWidth, 10);
        
        var posicao_y = this.rolagem_vertical_da_janela + (window.innerHeight - altura_da_div) / 2;
        if(window.innerHeight <= altura_da_div){
          posicao_y = this.rolagem_vertical_da_janela + 2;
          if(window.innerWidth <= largura_da_div){
            posicao_y = this.posicao_y_do_link_nome_da_pessoa + this.rolagem_vertical_da_janela;
          }
        }
        
        div_visualizar_pessoa.style.position = "absolute";
        div_visualizar_pessoa.style.top = posicao_y + "px";
        div_visualizar_pessoa.style.left = posicao_x + "px";
      },
      id_da_pessoa_do_popup_editar(valor_atual, valor_anterior) {
        const div_editar_pessoa = document.getElementById("div_editar_pessoa");
        if(valor_atual === null){
          div_editar_pessoa.classList.add("tag_oculta");
          return;
        }else if(valor_atual === "reposicionar"){
          /* Aciona o watch novamente */
          valor_atual = valor_anterior;
          this.id_da_pessoa_do_popup_editar = valor_atual;
          return;
        }
        
        div_editar_pessoa.classList.remove("tag_oculta");
        
        this.mensagem_editar_pessoa = "";
        this.tipo_da_mensagem_editar_pessoa = null;
        
        let largura_da_div = 0;
        var estilo_computado = window.getComputedStyle(div_editar_pessoa);
        largura_da_div += parseInt(estilo_computado.borderLeftWidth, 10);
        largura_da_div += parseInt(estilo_computado.paddingLeft, 10);
        largura_da_div += parseInt(estilo_computado.width, 10);
        largura_da_div += parseInt(estilo_computado.paddingRight, 10);
        largura_da_div += parseInt(estilo_computado.borderRightWidth, 10);
        
        const tag_html = document.querySelector("html");
        
        let largura_da_tag_html = 0;
        var estilo_computado = window.getComputedStyle(tag_html);
        largura_da_tag_html += parseInt(estilo_computado.width, 10);
        
        var posicao_x = largura_da_tag_html / 2 - largura_da_div / 2;
        if(window.innerWidth <= largura_da_div){
          posicao_x = 0;
        }
        
        let altura_da_div = 0;
        var estilo_computado = window.getComputedStyle(div_editar_pessoa);
        altura_da_div += parseInt(estilo_computado.borderTopWidth, 10);
        altura_da_div += parseInt(estilo_computado.paddingTop, 10);
        altura_da_div += parseInt(estilo_computado.height, 10);
        altura_da_div += parseInt(estilo_computado.paddingBottom, 10);
        altura_da_div += parseInt(estilo_computado.borderBottomWidth, 10);
        
        var posicao_y = this.rolagem_vertical_da_janela + (window.innerHeight - altura_da_div) / 2;
        if(window.innerHeight <= altura_da_div){
          posicao_y = this.rolagem_vertical_da_janela + 2;
          if(window.innerWidth <= largura_da_div){
            posicao_y = this.posicao_y_do_link_editar_pessoa + this.rolagem_vertical_da_janela;
          }
        }
        
        div_editar_pessoa.style.position = "absolute";
        div_editar_pessoa.style.top = posicao_y + "px";
        div_editar_pessoa.style.left = posicao_x + "px";
      },
      id_da_pessoa_do_popup_excluir(valor_atual, valor_anterior) {
        const div_excluir_pessoa = document.getElementById("div_excluir_pessoa");
        if(valor_atual === null){
          div_excluir_pessoa.classList.add("tag_oculta");
          return;
        }else if(valor_atual === "reposicionar"){
          /* Aciona o watch novamente */
          valor_atual = valor_anterior;
          this.id_da_pessoa_do_popup_excluir = valor_atual;
          return;
        }
        
        div_excluir_pessoa.classList.remove("tag_oculta");
        
        this.mensagem_excluir_pessoa = "";
        this.tipo_da_mensagem_excluir_pessoa = null;
        
        let largura_da_div = 0;
        var estilo_computado = window.getComputedStyle(div_excluir_pessoa);
        largura_da_div += parseInt(estilo_computado.borderLeftWidth, 10);
        largura_da_div += parseInt(estilo_computado.paddingLeft, 10);
        largura_da_div += parseInt(estilo_computado.width, 10);
        largura_da_div += parseInt(estilo_computado.paddingRight, 10);
        largura_da_div += parseInt(estilo_computado.borderRightWidth, 10);
        
        const tag_html = document.querySelector("html");
        
        let largura_da_tag_html = 0;
        var estilo_computado = window.getComputedStyle(tag_html);
        largura_da_tag_html += parseInt(estilo_computado.width, 10);
        
        var posicao_x = largura_da_tag_html / 2 - largura_da_div / 2;
        if(window.innerWidth <= largura_da_div){
          posicao_x = 0;
        }
        
        let altura_da_div = 0;
        var estilo_computado = window.getComputedStyle(div_excluir_pessoa);
        altura_da_div += parseInt(estilo_computado.borderTopWidth, 10);
        altura_da_div += parseInt(estilo_computado.paddingTop, 10);
        altura_da_div += parseInt(estilo_computado.height, 10);
        altura_da_div += parseInt(estilo_computado.paddingBottom, 10);
        altura_da_div += parseInt(estilo_computado.borderBottomWidth, 10);
        
        var posicao_y = this.posicao_y_do_link_excluir_pessoa + this.rolagem_vertical_da_janela - altura_da_div / 2;
        if(window.innerHeight <= altura_da_div){
          posicao_y = this.rolagem_vertical_da_janela + 2;
        }
        if(window.innerWidth <= largura_da_div){
          posicao_y = this.posicao_y_do_link_excluir_pessoa + this.rolagem_vertical_da_janela;
        }
        
        div_excluir_pessoa.style.position = "absolute";
        div_excluir_pessoa.style.top = posicao_y + "px";
        div_excluir_pessoa.style.left = posicao_x + "px";
      }
    }
  }
</script>

<template>
  <TemplateLayout :template_layout="vue_template_layout">
    <template #conteudo>
      <div id="div_pagina_tudo_em_um">
        <h2 id="h2_titulo_da_pagina">
          <span>Tudo em um</span>
        </h2>
        <div id="div_opcoes_da_pagina">
          <h3 id="h3_titulo_das_opcoes_da_pagina">
            <span>Opções</span>
          </h3>
          <div id="div_lista_de_opcoes_da_pagina">
            <a id="link_cadastrar_pessoa" class="opcao_da_pagina" @click="exibir_popup_cadastrar_pessoa" 
               href="/cadastrar_pessoa">Cadastrar Pessoa</a>
            <div id="div_cadastrar_pessoa" class="tag_oculta" @click="manter_ou_remover_popups_da_div_cadastrar_pessoa">
              <div class="div_fechar" @click="remover_popups">
                <span>X</span>
              </div>
              <h3 id="h3_titulo_cadastrar_pessoa">
                <span>Cadastrar Pessoa</span>
              </h3>
              <div id="div_legenda_do_formulario_cadastrar_pessoa">
                <span class="marcador_de_obrigatoriedade">* </span>
                <span>Indica que o campo é de preenchimento obrigatório.</span>
              </div>
              <div id="div_cadastrar_nome">
                <div id="div_label_cadastrar_nome">
                  <label id="label_cadastrar_nome" for="campo_cadastrar_nome">
                    <span class="marcador_de_obrigatoriedade">* </span>
                    <span>Nome</span>
                  </label>
                </div>
                <div id="div_campo_cadastrar_nome">
                  <input type="text" id="campo_cadastrar_nome" name="nome" v-model="cadastrar_nome" autocomplete="off"/>
                </div>
              </div>
              <div id="div_cadastrar_sobrenome">
                <div id="div_label_cadastrar_sobrenome">
                  <label id="label_cadastrar_sobrenome" for="campo_cadastrar_sobrenome">
                    <span class="marcador_de_obrigatoriedade">* </span>
                    <span>Sobrenome</span>
                  </label>
                </div>
                <div id="div_campo_cadastrar_sobrenome">
                  <input type="text" id="campo_cadastrar_sobrenome" name="sobrenome" v-model="cadastrar_sobrenome" 
                         autocomplete="off"/>
                </div>
              </div>
              <div id="div_cadastrar_cpf">
                <div id="div_label_cadastrar_cpf">
                  <label id="label_cadastrar_cpf" for="campo_cadastrar_cpf">
                    <span class="marcador_de_obrigatoriedade">* </span>
                    <span>CPF</span>
                  </label>
                </div>
                <div id="div_campo_cadastrar_cpf">
                  <input type="text" id="campo_cadastrar_cpf" name="cpf" v-model="cadastrar_cpf" 
                         @keyup="aplicar_mascara_para_o_campo_cadastrar_cpf" autocomplete="off"/>
                </div>
              </div>
              <div id="div_cadastrar_data_de_nascimento">
                <div id="div_label_cadastrar_data_de_nascimento">
                  <label id="label_cadastrar_data_de_nascimento" for="campo_cadastrar_data_de_nascimento">
                    <span class="marcador_de_obrigatoriedade">* </span>
                    <span>Data de nascimento</span>
                  </label>
                </div>
                <div id="div_campo_cadastrar_data_de_nascimento">
                  <input type="text" id="campo_cadastrar_data_de_nascimento" name="data_de_nascimento" 
                         v-model="cadastrar_data_de_nascimento" 
                         @mouseenter="colocar_borda_hover_no_campo_cadastrar_data_de_nascimento" 
                         @mouseleave="colocar_borda_normal_no_campo_cadastrar_data_de_nascimento" autocomplete="off"/>
                  <span id="span_icone_de_calendario_do_campo_cadastrar_data_de_nascimento" 
                        @mouseenter="colocar_borda_hover_no_campo_cadastrar_data_de_nascimento" 
                        @mouseleave="colocar_borda_normal_no_campo_cadastrar_data_de_nascimento" 
                        @mousedown="desfaz_selecao_de_texto"
                        @click="colocar_ou_remover_popup_calendario_do_campo_cadastrar_data_de_nascimento"></span>
                  <SeletorDeData v-if="mostrar_calendario_do_campo_cadastrar_data_de_nascimento"
                                 id_do_campo="campo_cadastrar_data_de_nascimento"></SeletorDeData>
                </div>
              </div>
              <div id="div_cadastrar_sexo">
                <div id="div_label_lista_de_sexos_do_cadastrar">
                  <label id="label_lista_de_sexos_do_cadastrar">
                    <span class="marcador_de_obrigatoriedade">* </span>
                    <span>Sexo</span>
                  </label>
                </div>
                <div id="div_lista_de_sexos_do_cadastrar">
                <template v-for="(valor, chave) in vue_tudo_em_um.sexos">
                  <label class="item_da_lista_de_sexos_do_cadastrar">
                    <input type="radio" name="cadastrar_sexo" v-model="cadastrar_sexo" :value="chave" 
                           autocomplete="off"/>
                    <span>&nbsp;</span>
                    <span>{{valor}}</span>
                  </label>
                </template>
                </div>
              </div>
              <div id="div_cadastrar_setor">
                <div id="div_label_cadastrar_setor">
                  <label id="label_cadastrar_setor" for="caixa_de_selecao_cadastrar_setor">
                    <span class="marcador_de_obrigatoriedade">* </span>
                    <span>Setor</span>
                  </label>
                </div>
                <div id="div_caixa_de_selecao_cadastrar_setor">
                  <select id="caixa_de_selecao_cadastrar_setor" name="id_do_setor" v-model="cadastrar_setor" 
                          autocomplete="off">
                    <option value="">Selecione</option>
                    <option v-for="(setor, chave) in vue_tudo_em_um.setores" :value="setor.id">{{setor.nome}}</option>
                  </select>
                </div>
              </div>
              <div id="div_cadastrar_email">
                <div id="div_label_cadastrar_email">
                  <label id="label_cadastrar_email" for="campo_cadastrar_email">
                    <span class="marcador_de_obrigatoriedade">* </span>
                    <span>E-mail</span>
                  </label>
                </div>
                <div id="div_campo_cadastrar_email">
                  <input type="text" id="campo_cadastrar_email" name="email" v-model="cadastrar_email" 
                         autocomplete="off"/>
                </div>
              </div>
              <div id="div_cadastrar_telefone_fixo">
                <div id="div_label_cadastrar_telefone_fixo">
                  <label id="label_cadastrar_telefone_fixo" for="campo_cadastrar_telefone_fixo">
                    <span>Número do telefone fixo</span>
                  </label>
                </div>
                <div id="div_campo_cadastrar_telefone_fixo">
                  <input type="text" id="campo_cadastrar_telefone_fixo" name="telefone_fixo" 
                         v-model="cadastrar_telefone_fixo" @keyup="aplicar_mascara_para_o_campo_cadastrar_telefone_fixo" 
                         autocomplete="off"/>
                </div>
              </div>
              <div id="div_cadastrar_telefone_movel">
                <div id="div_label_cadastrar_telefone_movel">
                  <label id="label_cadastrar_telefone_movel" for="campo_cadastrar_telefone_movel">
                    <span>Número de celular</span>
                  </label>
                </div>
                <div id="div_campo_cadastrar_telefone_movel">
                  <input type="text" id="campo_cadastrar_telefone_movel" name="telefone_movel" 
                         v-model="cadastrar_telefone_movel" 
                         @keyup="aplicar_mascara_para_o_campo_cadastrar_telefone_movel" autocomplete="off"/>
                </div>
              </div>
              <div id="div_cadastrar_telefone_estrangeiro">
                <div id="div_label_cadastrar_telefone_estrangeiro">
                  <label id="label_cadastrar_telefone_estrangeiro" for="campo_cadastrar_telefone_estrangeiro">
                    <span>Número para contato no exterior</span>
                  </label>
                </div>
                <div id="div_campo_cadastrar_telefone_estrangeiro">
                  <input type="text" id="campo_cadastrar_telefone_estrangeiro" name="telefone_estrangeiro" 
                         v-model="cadastrar_telefone_estrangeiro" autocomplete="off"/>
                </div>
              </div>
              <div v-if="mensagem_cadastrar_pessoa" id="div_mensagem_cadastrar_pessoa" class="div_mensagem">
                <span id="span_mensagem_cadastrar_pessoa" 
                      :class="tipo_da_mensagem_cadastrar_pessoa">{{mensagem_cadastrar_pessoa}}</span>
              </div>
              <div id="div_botao_cadastrar">
                <button type="button" id="botao_cadastrar" @mouseleave="remover_foco_do_botao" 
                        @click="cadastrar">Cadastrar</button>
              </div>
            </div>
          </div>
        </div>
        <div id="div_filtros">
          <h3 id="h3_titulo_dos_filtros">
            <span>Buscar</span>
          </h3>
          <div id="div_filtro_nome">
            <div id="div_label_filtro_nome">
              <label id="label_filtro_nome" for="campo_filtro_nome">
                <span>Nome</span>
              </label>
            </div>
            <div id="div_campo_filtro_nome">
              <input type="text" id="campo_filtro_nome" name="filtro_nome" autocomplete="off" @input="filtrar_por_nome" 
                     :value="filtro_nome" placeholder="Parte do nome"/>
            </div>
          </div>
          <div id="div_filtro_cpf">
            <div id="div_label_filtro_cpf">
              <label id="label_filtro_cpf" for="campo_filtro_cpf">
                <span>CPF</span>
              </label>
            </div>
            <div id="div_campo_filtro_cpf">
              <input type="text" id="campo_filtro_cpf" name="filtro_cpf" autocomplete="off" @input="filtrar_por_cpf" 
                     :value="filtro_cpf" placeholder="CPF completo"/>
            </div>
          </div>
          <div id="div_filtro_data_de_nascimento">
            <div id="div_label_filtro_data_de_nascimento">
              <label id="label_filtro_data_de_nascimento" for="campo_filtro_data_de_nascimento">
                <span>Data de nascimento</span>
              </label>
            </div>
            <div id="div_campo_filtro_data_de_nascimento">
              <input type="text" id="campo_filtro_data_de_nascimento" name="filtro_data_de_nascimento" 
                     autocomplete="off" placeholder="dia/mês/ano" @input="filtrar_por_data_de_nascimento" 
                     @mouseenter="colocar_borda_hover_no_campo_filtro_data_de_nascimento" 
                     @mouseleave="colocar_borda_normal_no_campo_filtro_data_de_nascimento" 
                     :value="filtro_data_de_nascimento"/>
              <span id="span_icone_de_calendario_do_campo_filtro_data_de_nascimento" 
                    @mouseenter="colocar_borda_hover_no_campo_filtro_data_de_nascimento" 
                    @mouseleave="colocar_borda_normal_no_campo_filtro_data_de_nascimento" 
                    @mousedown="desfaz_selecao_de_texto"
                    @click="colocar_ou_remover_popup_calendario_do_campo_filtro_data_de_nascimento"></span>
              <SeletorDeData v-if="mostrar_calendario_do_campo_filtro_data_de_nascimento"
                             id_do_campo="campo_filtro_data_de_nascimento"></SeletorDeData>
            </div>
          </div>
          <div id="div_filtro_setor">
            <div id="div_label_filtro_setor">
              <label id="label_filtro_setor" for="caixa_de_selecao_filtro_setor">
                <span>Setor</span>
              </label>
            </div>
            <div id="div_caixa_de_selecao_filtro_setor">
              <select id="caixa_de_selecao_filtro_setor" name="filtro_id_do_setor" autocomplete="off" 
                      @change="filtrar_por_setor" :value="filtro_id_do_setor">
                <option value="">Selecione</option>
                <option v-for="(setor, chave) in vue_tudo_em_um.setores" :value="setor.id">{{setor.nome}}</option>
              </select>
            </div>
          </div>
          <div id="div_quantidade_por_pagina">
            <div id="div_label_quantidade_por_pagina">
              <label id="label_quantidade_por_pagina" for="caixa_de_selecao_quantidade_por_pagina">
                <span>Quantidade por página</span>
              </label>
            </div>
            <div id="div_caixa_de_selecao_quantidade_por_pagina">
              <select id="caixa_de_selecao_quantidade_por_pagina" name="quantidade_por_pagina" autocomplete="off" 
                      @change="mudar_quantidade_por_pagina" :value="quantidade_por_pagina">
                <option value="padrao">Selecione</option>
                <option v-for="(valor, chave) in vue_tudo_em_um.quantidades_por_pagina" :value="chave">{{valor}}</option>
              </select>
            </div>
          </div>
          <div id="div_botoes_dos_filtros">
            <input type="hidden" id="campo_ordenacao" name="ordenacao" :value="ordenacao"/>
            <button type="button" id="botao_buscar" @mouseleave="remover_foco_do_botao" @click="buscar">Buscar</button>
            <button type="button" id="botao_limpar" @mouseleave="remover_foco_do_botao" @click="limpar">Limpar</button>
          </div>
        </div>
        <div id="div_local_da_lista_de_pessoas">
          <h3 id="h3_titulo_da_lista_de_pessoas">
            <span id="span_titulo_da_lista_de_pessoas">Lista</span>
            <span v-if="status_da_busca" id="span_status_da_busca">{{status_da_busca}}</span>
          </h3>
          <div v-if="vue_tudo_em_um.lista_de_pessoas.pagina_atual" id="div_paginacao_de_cima_da_lista_de_pessoas">
            <a class="primeira_pagina" href="/tudo_em_um?pagina=1" @click="mudar_pagina">Primeira</a>
            <span>&nbsp;</span>
            <a v-if="vue_tudo_em_um.lista_de_pessoas.pagina_atual > 1" class="pagina_anterior" @click="mudar_pagina" 
               :href="'/tudo_em_um?pagina=' + (vue_tudo_em_um.lista_de_pessoas.pagina_atual - 1)">Anterior</a>
            <a v-else class="pagina_anterior" href="/tudo_em_um?pagina=1" @click="mudar_pagina">Anterior</a>
            <span>&nbsp;</span>
            <template v-for="pagina in lista_de_paginas">
              <span v-if="pagina === '...'">...</span>
              <a v-else-if="pagina == vue_tudo_em_um.lista_de_pessoas.pagina_atual" class="pagina_selecionada" 
                 :href="'/tudo_em_um?pagina=' + pagina" @click="mudar_pagina">{{pagina}}</a>
              <a v-else class="pagina" :href="'/tudo_em_um?pagina=' + pagina" @click="mudar_pagina">{{pagina}}</a>
              <span>&nbsp;</span>
            </template>
            <a v-if="vue_tudo_em_um.lista_de_pessoas.pagina_atual < vue_tudo_em_um.lista_de_pessoas.ultima_pagina" 
               :href="'/tudo_em_um?pagina=' + (vue_tudo_em_um.lista_de_pessoas.pagina_atual + 1)" 
               class="pagina_seguinte" @click="mudar_pagina">Seguinte</a>
            <a v-else :href="'/tudo_em_um?pagina=' + vue_tudo_em_um.lista_de_pessoas.ultima_pagina" 
               class="pagina_seguinte" @click="mudar_pagina">Seguinte</a>
            <span>&nbsp;</span>
            <a :href="'/tudo_em_um?pagina=' + vue_tudo_em_um.lista_de_pessoas.ultima_pagina" 
               class="ultima_pagina" @click="mudar_pagina">Última</a>
          </div>
          <div id="div_partes_da_lista_de_pessoas">
            <div id="div_parte_nome_da_lista_de_pessoas" class="parte_da_lista" @click="ordenar_por_nome_completo">
              <span>Nome{{vue_tudo_em_um.lista_de_pessoas.ordem_do_nome}}</span>
            </div>
            <div id="div_parte_cpf_da_lista_de_pessoas" class="parte_da_lista" @click="ordenar_por_cpf">
              <span>CPF{{vue_tudo_em_um.lista_de_pessoas.ordem_do_cpf}}</span>
            </div>
            <div id="div_parte_setor_da_lista_de_pessoas" class="parte_da_lista" @click="ordenar_por_setor">
              <span>Setor{{vue_tudo_em_um.lista_de_pessoas.ordem_do_setor}}</span>
            </div>
            <div id="div_parte_contato_da_lista_de_pessoas" class="parte_da_lista" @click="ordenar_por_contato">
              <span>Contato{{vue_tudo_em_um.lista_de_pessoas.ordem_do_contato}}</span>
            </div>
            <div id="div_parte_opcoes_da_lista_de_pessoas" class="parte_da_lista">
              <span>Opções</span>
            </div>
          </div>
          <div id="div_lista_de_pessoas">
            <template v-for="(pessoa, chave) in vue_tudo_em_um.lista_de_pessoas.pessoas">
              <div :class="['pessoa', (chave + 1) % 2 === 0 ? 'par' : 'impar']">
                <div class="local_do_nome_da_pessoa">
                  <a :href="'pessoa?id=' + pessoa.id" class="nome_da_pessoa" 
                     @click="colocar_popup_visualizar_pessoa">{{pessoa.nome_completo}}</a>
                  <div :id="'div_visualizar_pessoa_do_id_' + pessoa.id" class="div_visualizar_pessoa tag_oculta">
                    <div class="div_fechar" @click="remover_popups">
                      <span>X</span>
                    </div>
                    <h3 class="h3_titulo_visualizar_pessoa">
                      <span>Informações</span>
                    </h3>
                    <div class="div_visualizar_nome_completo informacao_da_pessoa">
                      <span class="parte_generica_da_informacao">Nome:</span>
                      <div class="local_da_parte_especifica_da_informacao">
                        <span class="parte_especifica_da_informacao">{{pessoa.nome_completo}}</span>
                      </div>
                    </div>
                    <div class="div_visualizar_cpf informacao_da_pessoa">
                      <span class="parte_generica_da_informacao">CPF:</span>
                      <div class="local_da_parte_especifica_da_informacao">
                        <span class="parte_especifica_da_informacao">{{pessoa.cpf}}</span>
                      </div>
                    </div>
                    <div class="div_visualizar_data_de_nascimento informacao_da_pessoa">
                      <span class="parte_generica_da_informacao">Data de nascimento:</span>
                      <div class="local_da_parte_especifica_da_informacao">
                        <span class="parte_especifica_da_informacao">{{pessoa.data_de_nascimento}}</span>
                      </div>
                    </div>
                    <div class="div_visualizar_idade informacao_da_pessoa">
                      <span class="parte_generica_da_informacao">Idade:</span>
                      <div class="local_da_parte_especifica_da_informacao">
                        <span class="parte_especifica_da_informacao">{{pessoa.idade}}</span>
                      </div>
                    </div>
                    <div class="div_visualizar_sexo informacao_da_pessoa">
                      <span class="parte_generica_da_informacao">Sexo:</span>
                      <div class="local_da_parte_especifica_da_informacao">
                        <span :class="['parte_especifica_da_informacao', pessoa.sexo_classe_css]">{{vue_tudo_em_um.sexos[pessoa.sexo]}}</span>
                      </div>
                    </div>
                    <div class="div_visualizar_setor informacao_da_pessoa">
                      <span class="parte_generica_da_informacao">Setor:</span>
                      <div class="local_da_parte_especifica_da_informacao">
                        <span class="parte_especifica_da_informacao">{{pessoa.nome_do_setor}}</span>
                      </div>
                    </div>
                    <div class="div_visualizar_email informacao_da_pessoa">
                      <span class="parte_generica_da_informacao">E-mail:</span>
                      <div class="local_da_parte_especifica_da_informacao">
                        <span class="parte_especifica_da_informacao">{{pessoa.email}}</span>
                      </div>
                    </div>
                    <div class="div_visualizar_telefone_fixo informacao_da_pessoa">
                      <span class="parte_generica_da_informacao">Número do telefone fixo:</span>
                      <div v-if="pessoa.telefone_fixo" class="local_da_parte_especifica_da_informacao">
                        <span class="parte_especifica_da_informacao">{{pessoa.telefone_fixo}}</span>
                      </div>
                      <div v-else class="local_da_parte_especifica_da_informacao">
                        <span class="sem_informacao">Não informado</span>
                      </div>
                    </div>
                    <div class="div_visualizar_telefone_movel informacao_da_pessoa">
                      <span class="parte_generica_da_informacao">Número de celular:</span>
                      <div v-if="pessoa.telefone_movel" class="local_da_parte_especifica_da_informacao">
                        <span class="parte_especifica_da_informacao">{{pessoa.telefone_movel}}</span>
                      </div>
                      <div v-else class="local_da_parte_especifica_da_informacao">
                        <span class="sem_informacao">Não informado</span>
                      </div>
                    </div>
                    <div class="div_visualizar_telefone_estrangeiro informacao_da_pessoa">
                      <span class="parte_generica_da_informacao">Número para contato no exterior:</span>
                      <div v-if="pessoa.telefone_estrangeiro" class="local_da_parte_especifica_da_informacao">
                        <span class="parte_especifica_da_informacao">{{pessoa.telefone_estrangeiro}}</span>
                      </div>
                      <div v-else class="local_da_parte_especifica_da_informacao">
                        <span class="sem_informacao">Não informado</span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="local_do_cpf_da_pessoa">
                  <span class="cpf_da_pessoa">{{pessoa.cpf}}</span>
                </div>
                <div class="local_do_nome_do_setor">
                  <span class="nome_do_setor" v-html="pessoa.nome_do_setor_com_quebra_de_linha"></span>
                </div>
                <div class="local_do_contato_da_pessoa">
                  <div class="contato_da_pessoa">
                    <span class="email_da_pessoa">{{pessoa.email}}</span>
                  </div>
                  <div v-if="pessoa.telefone_fixo" class="contato_da_pessoa">
                    <span class="telefone_fixo_da_pessoa">{{pessoa.telefone_fixo}}</span>
                  </div>
                  <div v-if="pessoa.telefone_movel" class="contato_da_pessoa">
                    <span class="telefone_movel_da_pessoa">{{pessoa.telefone_movel}}</span>
                  </div>
                  <div v-if="pessoa.telefone_estrangeiro" class="contato_da_pessoa">
                    <span class="telefone_estrangeiro_da_pessoa">{{pessoa.telefone_estrangeiro}}</span>
                  </div>
                </div>
                <div class="local_das_opcoes_do_item_da_lista">
                  <div class="opcao_do_item_da_lista">
                    <a :href="'editar_pessoa?id=' + pessoa.id" class="link_editar_pessoa"
                       @click="exibir_popup_editar_pessoa(pessoa.nome, pessoa.sobrenome, pessoa.cpf, 
                       pessoa.data_de_nascimento, pessoa.sexo, pessoa.id_do_setor, pessoa.email, pessoa.telefone_fixo, 
                       pessoa.telefone_movel, pessoa.telefone_estrangeiro, pessoa.id, $event)">Editar</a>
                  </div>
                  <div class="opcao_do_item_da_lista">
                    <a :href="'excluir_pessoa?id=' + pessoa.id" class="link_excluir_pessoa"
                       @click="exibir_popup_excluir_pessoa(pessoa.nome_completo, pessoa.cpf, pessoa.nome_do_setor, 
                       pessoa.id, $event)">Excluir</a>
                  </div>
                </div>
              </div>
            </template>
            <div v-if="vue_tudo_em_um.lista_de_pessoas.pessoas.length === 0" id="div_mensagem_quando_nao_ha_pessoas">
              <span id="span_mensagem_quando_nao_ha_pessoas">Nenhuma pessoa foi encontrada, limpe os filtros ou busque por outras informações.</span>
            </div>
          </div>
          <div v-if="vue_tudo_em_um.lista_de_pessoas.pagina_atual" id="div_paginacao_de_baixo_da_lista_de_pessoas">
            <a class="primeira_pagina" href="/tudo_em_um?pagina=1" @click="mudar_pagina">Primeira</a>
            <span>&nbsp;</span>
            <a v-if="vue_tudo_em_um.lista_de_pessoas.pagina_atual > 1" class="pagina_anterior" @click="mudar_pagina" 
               :href="'/tudo_em_um?pagina=' + (vue_tudo_em_um.lista_de_pessoas.pagina_atual - 1)">Anterior</a>
            <a v-else class="pagina_anterior" href="/tudo_em_um?pagina=1" @click="mudar_pagina">Anterior</a>
            <span>&nbsp;</span>
            <template v-for="pagina in lista_de_paginas">
              <span v-if="pagina === '...'">...</span>
              <a v-else-if="pagina == vue_tudo_em_um.lista_de_pessoas.pagina_atual" class="pagina_selecionada" 
                 :href="'/tudo_em_um?pagina=' + pagina" @click="mudar_pagina">{{pagina}}</a>
              <a v-else class="pagina" :href="'/tudo_em_um?pagina=' + pagina" @click="mudar_pagina">{{pagina}}</a>
              <span>&nbsp;</span>
            </template>
            <a v-if="vue_tudo_em_um.lista_de_pessoas.pagina_atual < vue_tudo_em_um.lista_de_pessoas.ultima_pagina" 
               :href="'/tudo_em_um?pagina=' + (vue_tudo_em_um.lista_de_pessoas.pagina_atual + 1)" 
               class="pagina_seguinte" @click="mudar_pagina">Seguinte</a>
            <a v-else :href="'/tudo_em_um?pagina=' + vue_tudo_em_um.lista_de_pessoas.ultima_pagina" 
               class="pagina_seguinte" @click="mudar_pagina">Seguinte</a>
            <span>&nbsp;</span>
            <a :href="'/tudo_em_um?pagina=' + vue_tudo_em_um.lista_de_pessoas.ultima_pagina" 
               class="ultima_pagina" @click="mudar_pagina">Última</a>
          </div>
        </div>
        <div id="div_editar_pessoa" class="tag_oculta" @click="manter_ou_remover_popups_da_div_editar_pessoa">
          <div class="div_fechar" @click="remover_popups">
            <span>X</span>
          </div>
          <h3 id="h3_titulo_editar_pessoa">
            <span>Editar Pessoa</span>
          </h3>
          <div id="div_legenda_do_formulario_editar_pessoa">
            <span class="marcador_de_obrigatoriedade">* </span>
            <span>Indica que o campo é de preenchimento obrigatório.</span>
          </div>
          <div id="div_editar_nome">
            <div id="div_label_editar_nome">
              <label id="label_editar_nome" for="campo_editar_nome">
                <span class="marcador_de_obrigatoriedade">* </span>
                <span>Nome</span>
              </label>
            </div>
            <div id="div_campo_editar_nome">
              <input type="text" id="campo_editar_nome" name="nome" v-model="editar_nome" autocomplete="off"/>
            </div>
          </div>
          <div id="div_editar_sobrenome">
            <div id="div_label_editar_sobrenome">
              <label id="label_editar_sobrenome" for="campo_editar_sobrenome">
                <span class="marcador_de_obrigatoriedade">* </span>
                <span>Sobrenome</span>
              </label>
            </div>
            <div id="div_campo_editar_sobrenome">
              <input type="text" id="campo_editar_sobrenome" name="sobrenome" v-model="editar_sobrenome" 
                     autocomplete="off"/>
            </div>
          </div>
          <div id="div_editar_cpf">
            <div id="div_label_editar_cpf">
              <label id="label_editar_cpf" for="campo_editar_cpf">
                <span class="marcador_de_obrigatoriedade">* </span>
                <span>CPF</span>
              </label>
            </div>
            <div id="div_campo_editar_cpf">
              <input type="text" id="campo_editar_cpf" name="cpf" v-model="editar_cpf" 
                     @keyup="aplicar_mascara_para_o_campo_editar_cpf" autocomplete="off"/>
            </div>
          </div>
          <div id="div_editar_data_de_nascimento">
            <div id="div_label_editar_data_de_nascimento">
              <label id="label_editar_data_de_nascimento" for="campo_editar_data_de_nascimento">
                <span class="marcador_de_obrigatoriedade">* </span>
                <span>Data de nascimento</span>
              </label>
            </div>
            <div id="div_campo_editar_data_de_nascimento">
              <input type="text" id="campo_editar_data_de_nascimento" name="data_de_nascimento"
                     v-model="editar_data_de_nascimento" 
                     @mouseenter="colocar_borda_hover_no_campo_editar_data_de_nascimento" 
                     @mouseleave="colocar_borda_normal_no_campo_editar_data_de_nascimento" autocomplete="off"/>
              <span id="span_icone_de_calendario_do_campo_editar_data_de_nascimento" 
                    @mouseenter="colocar_borda_hover_no_campo_editar_data_de_nascimento" 
                    @mouseleave="colocar_borda_normal_no_campo_editar_data_de_nascimento" 
                    @mousedown="desfaz_selecao_de_texto"
                    @click="colocar_ou_remover_popup_calendario_do_campo_editar_data_de_nascimento"></span>
              <SeletorDeData v-if="mostrar_calendario_do_campo_editar_data_de_nascimento"
                             id_do_campo="campo_editar_data_de_nascimento"></SeletorDeData>
            </div>
          </div>
          <div id="div_editar_sexo">
            <div id="div_label_lista_de_sexos_do_editar">
              <label id="label_lista_de_sexos_do_editar">
                <span class="marcador_de_obrigatoriedade">* </span>
                <span>Sexo</span>
              </label>
            </div>
            <div id="div_lista_de_sexos_do_editar">
              <template v-for="(valor, chave) in vue_tudo_em_um.sexos">
                <label class="item_da_lista_de_sexos_do_editar">
                  <input type="radio" name="editar_sexo" v-model="editar_sexo" :value="chave" autocomplete="off"/>
                  <span>&nbsp;</span>
                  <span>{{valor}}</span>
                </label>
              </template>
            </div>
          </div>
          <div id="div_editar_setor">
            <div id="div_label_editar_setor">
              <label id="label_editar_setor" for="caixa_de_selecao_editar_setor">
                <span class="marcador_de_obrigatoriedade">* </span>
                <span>Setor</span>
              </label>
            </div>
            <div id="div_caixa_de_selecao_editar_setor">
              <select id="caixa_de_selecao_editar_setor" name="id_do_setor" v-model="editar_setor" autocomplete="off">
                <option value="">Selecione</option>
                <option v-for="(setor, chave) in vue_tudo_em_um.setores" :value="setor.id">{{setor.nome}}</option>
              </select>
            </div>
          </div>
          <div id="div_editar_email">
            <div id="div_label_editar_email">
              <label id="label_editar_email" for="campo_editar_email">
                <span class="marcador_de_obrigatoriedade">* </span>
                <span>E-mail</span>
              </label>
            </div>
            <div id="div_campo_editar_email">
              <input type="text" id="campo_editar_email" name="email" v-model="editar_email" autocomplete="off"/>
            </div>
          </div>
          <div id="div_editar_telefone_fixo">
            <div id="div_label_editar_telefone_fixo">
              <label id="label_editar_telefone_fixo" for="campo_editar_telefone_fixo">
                <span>Número do telefone fixo</span>
              </label>
            </div>
            <div id="div_campo_editar_telefone_fixo">
              <input type="text" id="campo_editar_telefone_fixo" name="telefone_fixo" v-model="editar_telefone_fixo" 
                     @keyup="aplicar_mascara_para_o_campo_editar_telefone_fixo" autocomplete="off"/>
            </div>
          </div>
          <div id="div_editar_telefone_movel">
            <div id="div_label_editar_telefone_movel">
              <label id="label_editar_telefone_movel" for="campo_editar_telefone_movel">
                <span>Número de celular</span>
              </label>
            </div>
            <div id="div_campo_editar_telefone_movel">
              <input type="text" id="campo_editar_telefone_movel" name="telefone_movel" 
                     @keyup="aplicar_mascara_para_o_campo_editar_telefone_movel" v-model="editar_telefone_movel" 
                     autocomplete="off"/>
            </div>
          </div>
          <div id="div_editar_telefone_estrangeiro">
            <div id="div_label_editar_telefone_estrangeiro">
              <label id="label_editar_telefone_estrangeiro" for="campo_editar_telefone_estrangeiro">
                <span>Número para contato no exterior</span>
              </label>
            </div>
            <div id="div_campo_editar_telefone_estrangeiro">
              <input type="text" id="campo_editar_telefone_estrangeiro" name="telefone_estrangeiro" 
                     v-model="editar_telefone_estrangeiro" autocomplete="off"/>
            </div>
          </div>
          <div v-if="mensagem_editar_pessoa" id="div_mensagem_editar_pessoa" class="div_mensagem">
            <span id="span_mensagem_editar_pessoa" 
                  :class="tipo_da_mensagem_editar_pessoa">{{mensagem_editar_pessoa}}</span>
          </div>
          <div id="div_botao_editar">
            <button type="button" id="botao_editar" @mouseleave="remover_foco_do_botao" @click="editar">Editar</button>
          </div>
        </div>
        <div id="div_excluir_pessoa" class="tag_oculta">
          <div class="div_fechar" @click="remover_popups">
            <span>X</span>
          </div>
          <h3 id="h3_titulo_excluir_pessoa">
            <span>Excluir Pessoa</span>
          </h3>
          <div id="div_texto_confirmar_exclusao_de_pessoa">
            <span>
              Tem certeza que você deseja excluir definitivamente, do banco de dados deste sistema, 
              {{nome_completo_da_pessoa_que_sera_excluida}}, CPF {{cpf_da_pessoa_que_sera_excluida}}, 
              cujo setor é o {{nome_do_setor_da_pessoa_que_sera_excluida}}?
            </span>
          </div>
          <div v-if="mensagem_excluir_pessoa" id="div_mensagem_excluir_pessoa" class="div_mensagem">
            <span id="span_mensagem_excluir_pessoa" 
                  :class="tipo_da_mensagem_excluir_pessoa">{{mensagem_excluir_pessoa}}</span>
          </div>
          <div id="div_botao_excluir">
            <button type="button" id="botao_excluir" @mouseleave="remover_foco_do_botao" 
                    @click="excluir">Excluir Pessoa</button>
          </div>
        </div>
      </div>
    </template>
  </TemplateLayout>
  <Head>
    <title>Tudo em um</title>
    <link :href="endereco_do_arquivo_css" type="text/css" rel="stylesheet"/>
  </Head>
</template>
