<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

final class InicioController extends TemplateLayoutController{

  public function carregar_pagina(){
    $valores = $this->valores_do_template_layout();

    return Inertia::render('inicio/inicio', $valores);
  }

}
