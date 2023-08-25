<script>
  import TemplateLayout from "../template_layout.vue";
  import SeletorDeData from "./seletor_de_data.vue";
  import {Head} from "@inertiajs/vue3";
  
  export default{
    props: [
      "template_layout",
      "listar_pessoas"
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
        vue_listar_pessoas: this.listar_pessoas,
        
        /* Propriedades novas e seus valores iniciais */
        endereco_do_arquivo_css: "/css/" + this.template_layout.visual_escolhido + "/listar_pessoas.css",
        
        pagina_selecionada: this.listar_pessoas.lista_de_pessoas.pagina_atual,
        
        ultimo_valor_campo_filtro_cpf: "",
        mostrar_calendario_do_campo_filtro_data_de_nascimento: false,
        
        lista_de_paginas: new Array(0)
      }
    },
    created(){
      /* Se o valor advindo do Laravel não for um dos valores possíveis, a caixa de seleção terá o valor default. */
      var valor_default_para_a_caixa_de_selecao = true;
      for(let i = 0; i < this.vue_listar_pessoas.setores.length; i++){
        if(this.vue_listar_pessoas.setores[i].id == this.vue_listar_pessoas.lista_de_pessoas.filtro_id_do_setor){
          valor_default_para_a_caixa_de_selecao = false;
          break;
        }
      }
      if(valor_default_para_a_caixa_de_selecao){
        this.vue_listar_pessoas.lista_de_pessoas.filtro_id_do_setor = "";
      }
      
      if(Object.keys(this.vue_listar_pessoas.quantidades_por_pagina).indexOf(this.vue_listar_pessoas.lista_de_pessoas.quantidade_por_pagina.toString()) === -1){
        this.vue_listar_pessoas.lista_de_pessoas.quantidade_por_pagina = "padrao";
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
            case "campo_filtro_data_de_nascimento":
            case "span_icone_de_calendario_do_campo_filtro_data_de_nascimento":
            case "div_calendario":
              break ciclo;
            break;
          }
          
          tag_alvo = tag_alvo.parentNode;
        }
      }.bind(this));
    },
    methods: {
      criar_lista_de_paginas(){
        const pagina_atual = this.vue_listar_pessoas.lista_de_pessoas.pagina_atual;
        const inicio_da_sequencia = Math.max(pagina_atual - 3, 1);
        const final_da_sequencia = Math.min(pagina_atual + 3, this.vue_listar_pessoas.lista_de_pessoas.ultima_pagina);
        const lista_de_paginas = new Array(0);
        
        if(inicio_da_sequencia != 1){
          lista_de_paginas.push("...");
        }
        
        for(var pagina=inicio_da_sequencia; pagina <= final_da_sequencia; pagina++){
          lista_de_paginas.push(pagina);
        }
        
        if(final_da_sequencia != this.vue_listar_pessoas.lista_de_pessoas.ultima_pagina){
          lista_de_paginas.push("...");
        }
        
        this.lista_de_paginas = lista_de_paginas;
      },
      aplicar_mascara_para_o_campo_filtro_cpf(evento){
        evento.preventDefault();
        
        const campo_filtro_cpf = evento.currentTarget;
        
        let posicao_do_cursor = campo_filtro_cpf.selectionStart;
        if(typeof posicao_do_cursor !== "number"){
          /* Se o navegador não suportar, cancela. */
          return;
        }
        
        const teclas_para_ignorar = [
          "Enter", "Tab", "Shift", "Control", "Backspace", "Home", "End", "Delete", 
          "Up", "Right", "Down", "Left", "ArrowUp", "ArrowRight", "ArrowDown", "ArrowLeft"
        ];
        if(teclas_para_ignorar.indexOf(evento.key) >= 0){
          this.ultimo_valor_campo_filtro_cpf = campo_filtro_cpf.value;
          return;
        }
        
        campo_filtro_cpf.value = campo_filtro_cpf.value.replace(/[^0-9]/g, "");
        
        if(campo_filtro_cpf.value.length >= 3){
          campo_filtro_cpf.value = campo_filtro_cpf.value.slice(0, 3) + "." + campo_filtro_cpf.value.slice(3);
          if((posicao_do_cursor === 3 || posicao_do_cursor === 4) && campo_filtro_cpf.value.length > this.ultimo_valor_campo_filtro_cpf.length){
            posicao_do_cursor++;
          }else if(campo_filtro_cpf.value.length >= 7 && posicao_do_cursor === 4){
            posicao_do_cursor++;
          }
        }
        if(campo_filtro_cpf.value.length >= 7){
          campo_filtro_cpf.value = campo_filtro_cpf.value.slice(0, 7) + "." + campo_filtro_cpf.value.slice(7);
          if((posicao_do_cursor === 7 || posicao_do_cursor === 8) && campo_filtro_cpf.value.length > this.ultimo_valor_campo_filtro_cpf.length){
            posicao_do_cursor++;
          }else if(campo_filtro_cpf.value.length >= 11 && posicao_do_cursor === 8){
            posicao_do_cursor++;
          }
        }
        if(campo_filtro_cpf.value.length >= 11){
          campo_filtro_cpf.value = campo_filtro_cpf.value.slice(0, 11) + "-" + campo_filtro_cpf.value.slice(11);
          if((posicao_do_cursor === 11 || posicao_do_cursor === 12) && campo_filtro_cpf.value.length > this.ultimo_valor_campo_filtro_cpf.length){
            posicao_do_cursor++;
          }
        }
        if(campo_filtro_cpf.value.length > 14){
          campo_filtro_cpf.value = campo_filtro_cpf.value.slice(0, 14);
        }
        
        campo_filtro_cpf.setSelectionRange(posicao_do_cursor, posicao_do_cursor);
        
        this.ultimo_valor_campo_filtro_cpf = campo_filtro_cpf.value;
        this.vue_listar_pessoas.lista_de_pessoas.filtro_cpf = campo_filtro_cpf.value;
      },
      colocar_borda_hover_no_campo(evento){
        let tag_alvo = evento.currentTarget;
        
        if(tag_alvo.id === "span_icone_de_calendario_do_campo_filtro_data_de_nascimento"){
          tag_alvo = tag_alvo.parentNode.querySelector("#campo_filtro_data_de_nascimento");
        }
        if(tag_alvo.id === "campo_filtro_data_de_nascimento"){
          tag_alvo.style.border = "1px solid #8080C8";
        }
      },
      colocar_borda_normal_no_campo(evento){
        let tag_alvo = evento.currentTarget;
        
        if(tag_alvo.id === "span_icone_de_calendario_do_campo_filtro_data_de_nascimento"){
          tag_alvo = tag_alvo.parentNode.querySelector("#campo_filtro_data_de_nascimento");
        }
        if(tag_alvo.id === "campo_filtro_data_de_nascimento"){
          tag_alvo.style.border = "1px solid #C8C8C8";
        }
      },
      desfaz_selecao_de_texto(evento){
        evento.preventDefault();
      },
      remover_foco_do_botao(evento){
        evento.currentTarget.blur();
      },
      enviar_formulario_de_filtro(){
        var elemento_formulario = document.createElement("form");
        elemento_formulario.setAttribute("class", "tag_oculta");
        elemento_formulario.setAttribute("method", "get");
        elemento_formulario.setAttribute("action", "/listar_pessoas");
        
        let quantidade_nao_enviada = 0;
        
        if(this.vue_listar_pessoas.lista_de_pessoas.filtro_nome == ""){
          quantidade_nao_enviada++;
        }else{
          var elemento_campo = document.createElement("input");
          elemento_campo.setAttribute("name", "filtro_nome");
          elemento_campo.setAttribute("value", this.vue_listar_pessoas.lista_de_pessoas.filtro_nome);
          elemento_formulario.appendChild(elemento_campo);
        }
        if(this.vue_listar_pessoas.lista_de_pessoas.filtro_cpf == ""){
          quantidade_nao_enviada++;
        }else{
          var elemento_campo = document.createElement("input");
          elemento_campo.setAttribute("name", "filtro_cpf");
          elemento_campo.setAttribute("value", this.vue_listar_pessoas.lista_de_pessoas.filtro_cpf);
          elemento_formulario.appendChild(elemento_campo);
        }
        if(this.vue_listar_pessoas.lista_de_pessoas.filtro_data_de_nascimento == ""){
          quantidade_nao_enviada++;
        }else{
          var elemento_campo = document.createElement("input");
          elemento_campo.setAttribute("name", "filtro_data_de_nascimento");
          elemento_campo.setAttribute("value", this.vue_listar_pessoas.lista_de_pessoas.filtro_data_de_nascimento);
          elemento_formulario.appendChild(elemento_campo);
        }
        if(this.vue_listar_pessoas.lista_de_pessoas.filtro_id_do_setor == ""){
          quantidade_nao_enviada++;
        }else{
          var elemento_campo = document.createElement("input");
          elemento_campo.setAttribute("name", "filtro_id_do_setor");
          elemento_campo.setAttribute("value", this.vue_listar_pessoas.lista_de_pessoas.filtro_id_do_setor);
          elemento_formulario.appendChild(elemento_campo);
        }
        if(this.vue_listar_pessoas.lista_de_pessoas.quantidade_por_pagina == "padrao"){
          quantidade_nao_enviada++;
        }else{
          var elemento_campo = document.createElement("input");
          elemento_campo.setAttribute("name", "quantidade_por_pagina");
          elemento_campo.setAttribute("value", this.vue_listar_pessoas.lista_de_pessoas.quantidade_por_pagina);
          elemento_formulario.appendChild(elemento_campo);
        }
        if(this.vue_listar_pessoas.lista_de_pessoas.ordenacao == "padrao"){
          quantidade_nao_enviada++;
        }else{
          var elemento_campo = document.createElement("input");
          elemento_campo.setAttribute("name", "ordenacao");
          elemento_campo.setAttribute("value", this.vue_listar_pessoas.lista_de_pessoas.ordenacao);
          elemento_formulario.appendChild(elemento_campo);
        }
        if(this.pagina_selecionada == null){
          quantidade_nao_enviada++;
        }else{
          var elemento_campo = document.createElement("input");
          elemento_campo.setAttribute("name", "pagina");
          elemento_campo.setAttribute("value", this.pagina_selecionada);
          elemento_formulario.appendChild(elemento_campo);
        }
        
        if(quantidade_nao_enviada == 7){
          window.location.href = "/listar_pessoas";
        }else{
          document.body.appendChild(elemento_formulario);
          elemento_formulario.submit();
        }
      },
      buscar(evento){
        evento.preventDefault();
        
        evento.currentTarget.blur();
        
        this.pagina_selecionada = null;
        
        this.enviar_formulario_de_filtro();
      },
      redirecionar_sem_filtros(evento){
        evento.preventDefault();
        
        evento.currentTarget.blur();
        
        window.location.href = "/listar_pessoas";
      },
      mudar_pagina(evento){
        evento.preventDefault();
        
        const href = evento.currentTarget.getAttribute("href");
        const pagina = href.replace("/listar_pessoas?pagina=", "");
        
        this.pagina_selecionada = pagina;
        
        this.enviar_formulario_de_filtro();
      },
      ordenar_por_nome_completo(evento){
        this.pagina_selecionada = null;
        switch(this.vue_listar_pessoas.lista_de_pessoas.ordenacao){
          case "nome_completo_a_z":
            this.vue_listar_pessoas.lista_de_pessoas.ordenacao = "nome_completo_z_a";
            break;
          case "nome_completo_z_a":
            this.vue_listar_pessoas.lista_de_pessoas.ordenacao = "padrao";
            break;
          default:
            this.vue_listar_pessoas.lista_de_pessoas.ordenacao = "nome_completo_a_z";
            break;
        }
        this.enviar_formulario_de_filtro();
      },
      ordenar_por_cpf(evento){
        this.pagina_selecionada = null;
        switch(this.vue_listar_pessoas.lista_de_pessoas.ordenacao){
          case "cpf_crescente":
            this.vue_listar_pessoas.lista_de_pessoas.ordenacao = "cpf_decrescente";
            break;
          case "cpf_decrescente":
            this.vue_listar_pessoas.lista_de_pessoas.ordenacao = "padrao";
            break;
          default:
            this.vue_listar_pessoas.lista_de_pessoas.ordenacao = "cpf_crescente";
            break;
        }
        this.enviar_formulario_de_filtro();
      },
      ordenar_por_setor(evento){
        this.pagina_selecionada = null;
        switch(this.vue_listar_pessoas.lista_de_pessoas.ordenacao){
          case "setor_a_z":
            this.vue_listar_pessoas.lista_de_pessoas.ordenacao = "setor_z_a";
            break;
          case "setor_z_a":
            this.vue_listar_pessoas.lista_de_pessoas.ordenacao = "padrao";
            break;
          default:
            this.vue_listar_pessoas.lista_de_pessoas.ordenacao = "setor_a_z";
            break;
        }
        this.enviar_formulario_de_filtro();
      },
      ordenar_por_contato(evento){
        this.pagina_selecionada = null;
        switch(this.vue_listar_pessoas.lista_de_pessoas.ordenacao){
          case "contato_a_z":
            this.vue_listar_pessoas.lista_de_pessoas.ordenacao = "contato_z_a";
            break;
          case "contato_z_a":
            this.vue_listar_pessoas.lista_de_pessoas.ordenacao = "padrao";
            break;
          default:
            this.vue_listar_pessoas.lista_de_pessoas.ordenacao = "contato_a_z";
            break;
        }
        this.enviar_formulario_de_filtro();
      },
      remover_popups(){
        this.mostrar_calendario_do_campo_filtro_data_de_nascimento = false;
      },
      colocar_popup(evento){
        /* Se já tiver o popup, irá remover. */
        switch(evento.currentTarget.getAttribute("id")){
          case "span_icone_de_calendario_do_campo_filtro_data_de_nascimento":
            if(this.mostrar_calendario_do_campo_filtro_data_de_nascimento){
              this.mostrar_calendario_do_campo_filtro_data_de_nascimento = false;
            }else{
              this.remover_popups();
              this.mostrar_calendario_do_campo_filtro_data_de_nascimento = true;
            }
          break;
        }
      }
    }
  }
</script>

<template>
  <TemplateLayout :template_layout="vue_template_layout">
    <template #conteudo>
      <div id="div_pagina_listar_pessoas">
        <h2 id="h2_titulo_da_pagina">
          <span>Lista de Pessoas</span>
        </h2>
        <div id="div_opcoes_da_pagina">
          <h3 id="h3_titulo_das_opcoes_da_pagina">
            <span>Opções</span>
          </h3>
          <div id="div_lista_de_opcoes_da_pagina">
            <a id="link_cadastrar" class="opcao_da_pagina" href="/cadastrar_pessoa">Cadastrar Pessoa</a>
          </div>
        </div>
        <form id="form_filtros" method="get" action="/listar_pessoas">
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
              <input type="text" id="campo_filtro_nome" name="filtro_nome" autocomplete="off"
                     v-model="vue_listar_pessoas.lista_de_pessoas.filtro_nome" placeholder="Parte do nome"/>
            </div>
          </div>
          <div id="div_filtro_cpf">
            <div id="div_label_filtro_cpf">
              <label id="label_filtro_cpf" for="campo_filtro_cpf">
                <span>CPF</span>
              </label>
            </div>
            <div id="div_campo_filtro_cpf">
              <input type="text" id="campo_filtro_cpf" name="filtro_cpf" autocomplete="off"
                     @keyup="aplicar_mascara_para_o_campo_filtro_cpf" 
                     v-model="vue_listar_pessoas.lista_de_pessoas.filtro_cpf" placeholder="CPF completo"/>
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
                     autocomplete="off" v-model="vue_listar_pessoas.lista_de_pessoas.filtro_data_de_nascimento" 
                     @mouseenter="colocar_borda_hover_no_campo" @mouseleave="colocar_borda_normal_no_campo" 
                     placeholder="dia/mês/ano"/>
              <span id="span_icone_de_calendario_do_campo_filtro_data_de_nascimento" 
                    @mouseenter="colocar_borda_hover_no_campo" @mouseleave="colocar_borda_normal_no_campo" 
                    @mousedown="desfaz_selecao_de_texto" @click="colocar_popup"></span>
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
                      v-model="vue_listar_pessoas.lista_de_pessoas.filtro_id_do_setor">
                <option value="">Selecione</option>
                <option v-for="(setor, chave) in vue_listar_pessoas.setores" :value="setor.id">{{setor.nome}}</option>
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
                      v-model="vue_listar_pessoas.lista_de_pessoas.quantidade_por_pagina">
                <option value="padrao">Selecione</option>
                <option v-for="(valor, chave) in vue_listar_pessoas.quantidades_por_pagina" :value="chave">{{valor}}</option>
              </select>
            </div>
          </div>
          <div id="div_botoes_dos_filtros">
            <input type="hidden" id="campo_ordenacao" name="ordenacao" :value="vue_listar_pessoas.lista_de_pessoas.ordenacao"/>
            <input type="submit" id="botao_buscar" @mouseleave="remover_foco_do_botao" @click="buscar" value="Buscar"/>
            <input type="reset" id="botao_limpar" @mouseleave="remover_foco_do_botao" @click="redirecionar_sem_filtros" 
                   value="Limpar"/>
          </div>
        </form>
        <div id="div_local_da_lista_de_pessoas">
          <h3 id="h3_titulo_da_lista_de_pessoas">
            <span>Lista</span>
          </h3>
          <div v-if="vue_listar_pessoas.lista_de_pessoas.pagina_atual" id="div_paginacao_de_cima_da_lista_de_pessoas">
            <a class="primeira_pagina" href="/listar_pessoas?pagina=1" @click="mudar_pagina">Primeira</a>
            <span>&nbsp;</span>
            <a v-if="vue_listar_pessoas.lista_de_pessoas.pagina_atual > 1" class="pagina_anterior" @click="mudar_pagina" 
               :href="'/listar_pessoas?pagina=' + (vue_listar_pessoas.lista_de_pessoas.pagina_atual - 1)">Anterior</a>
            <a v-else class="pagina_anterior" href="/listar_pessoas?pagina=1" @click="mudar_pagina">Anterior</a>
            <span>&nbsp;</span>
            <template v-for="pagina in lista_de_paginas">
              <span v-if="pagina === '...'">...</span>
              <a v-else-if="pagina == vue_listar_pessoas.lista_de_pessoas.pagina_atual" class="pagina_selecionada" 
                 :href="'/listar_pessoas?pagina=' + pagina" @click="mudar_pagina">{{pagina}}</a>
              <a v-else class="pagina" :href="'/listar_pessoas?pagina=' + pagina" @click="mudar_pagina">{{pagina}}</a>
              <span>&nbsp;</span>
            </template>
            <a v-if="vue_listar_pessoas.lista_de_pessoas.pagina_atual < vue_listar_pessoas.lista_de_pessoas.ultima_pagina" 
               :href="'/listar_pessoas?pagina=' + (vue_listar_pessoas.lista_de_pessoas.pagina_atual + 1)" 
               class="pagina_seguinte" @click="mudar_pagina">Seguinte</a>
            <a v-else :href="'/listar_pessoas?pagina=' + vue_listar_pessoas.lista_de_pessoas.ultima_pagina" 
               class="pagina_seguinte" @click="mudar_pagina">Seguinte</a>
            <span>&nbsp;</span>
            <a :href="'/listar_pessoas?pagina=' + vue_listar_pessoas.lista_de_pessoas.ultima_pagina" 
               class="ultima_pagina" @click="mudar_pagina">Última</a>
          </div>
          <div id="div_partes_da_lista_de_pessoas">
            <div id="div_parte_nome_da_lista_de_pessoas" class="parte_da_lista" @click="ordenar_por_nome_completo">
              <span>Nome{{vue_listar_pessoas.lista_de_pessoas.ordem_do_nome}}</span>
            </div>
            <div id="div_parte_cpf_da_lista_de_pessoas" class="parte_da_lista" @click="ordenar_por_cpf">
              <span>CPF{{vue_listar_pessoas.lista_de_pessoas.ordem_do_cpf}}</span>
            </div>
            <div id="div_parte_setor_da_lista_de_pessoas" class="parte_da_lista" @click="ordenar_por_setor">
              <span>Setor{{vue_listar_pessoas.lista_de_pessoas.ordem_do_setor}}</span>
            </div>
            <div id="div_parte_contato_da_lista_de_pessoas" class="parte_da_lista" @click="ordenar_por_contato">
              <span>Contato{{vue_listar_pessoas.lista_de_pessoas.ordem_do_contato}}</span>
            </div>
            <div id="div_parte_opcoes_da_lista_de_pessoas" class="parte_da_lista">
              <span>Opções</span>
            </div>
          </div>
          <div id="div_lista_de_pessoas">
            <template v-for="(pessoa, chave) in vue_listar_pessoas.lista_de_pessoas.pessoas">
              <div :class="['pessoa', (chave + 1) % 2 === 0 ? 'par' : 'impar']">
                <div class="local_do_nome_da_pessoa">
                  <a :href="'pessoa?id=' + pessoa.id" class="nome_da_pessoa">{{pessoa.nome_completo}}</a>
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
                    <a :href="'editar_pessoa?id=' + pessoa.id" class="link_editar_pessoa">Editar</a>
                  </div>
                  <div class="opcao_do_item_da_lista">
                    <a :href="'excluir_pessoa?id=' + pessoa.id" class="link_excluir_pessoa">Excluir</a>
                  </div>
                </div>
              </div>
            </template>
            <div v-if="vue_listar_pessoas.lista_de_pessoas.pessoas.length === 0" id="div_mensagem_quando_nao_ha_pessoas">
              <span id="span_mensagem_quando_nao_ha_pessoas">Nenhuma pessoa foi encontrada, limpe os filtros ou busque por outras informações.</span>
            </div>
          </div>
          <div v-if="vue_listar_pessoas.lista_de_pessoas.pagina_atual" id="div_paginacao_de_baixo_da_lista_de_pessoas">
            <a class="primeira_pagina" href="/listar_pessoas?pagina=1" @click="mudar_pagina">Primeira</a>
            <span>&nbsp;</span>
            <a v-if="vue_listar_pessoas.lista_de_pessoas.pagina_atual > 1" class="pagina_anterior" @click="mudar_pagina" 
               :href="'/listar_pessoas?pagina=' + (vue_listar_pessoas.lista_de_pessoas.pagina_atual - 1)">Anterior</a>
            <a v-else class="pagina_anterior" href="/listar_pessoas?pagina=1" @click="mudar_pagina">Anterior</a>
            <span>&nbsp;</span>
            <template v-for="pagina in lista_de_paginas">
              <span v-if="pagina === '...'">...</span>
              <a v-else-if="pagina == vue_listar_pessoas.lista_de_pessoas.pagina_atual" class="pagina_selecionada" 
                 :href="'/listar_pessoas?pagina=' + pagina" @click="mudar_pagina">{{pagina}}</a>
              <a v-else class="pagina" :href="'/listar_pessoas?pagina=' + pagina" @click="mudar_pagina">{{pagina}}</a>
              <span>&nbsp;</span>
            </template>
            <a v-if="vue_listar_pessoas.lista_de_pessoas.pagina_atual < vue_listar_pessoas.lista_de_pessoas.ultima_pagina" 
               :href="'/listar_pessoas?pagina=' + (vue_listar_pessoas.lista_de_pessoas.pagina_atual + 1)" 
               class="pagina_seguinte" @click="mudar_pagina">Seguinte</a>
            <a v-else :href="'/listar_pessoas?pagina=' + vue_listar_pessoas.lista_de_pessoas.ultima_pagina" 
               class="pagina_seguinte" @click="mudar_pagina">Seguinte</a>
            <span>&nbsp;</span>
            <a :href="'/listar_pessoas?pagina=' + vue_listar_pessoas.lista_de_pessoas.ultima_pagina" 
               class="ultima_pagina" @click="mudar_pagina">Última</a>
          </div>
        </div>
      </div>
    </template>
  </TemplateLayout>
  <Head>
    <title>Listar Pessoas</title>
    <link :href="endereco_do_arquivo_css" type="text/css" rel="stylesheet"/>
  </Head>
</template>
