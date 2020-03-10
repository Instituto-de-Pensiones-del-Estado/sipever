<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css')}}" media="screen">
    <title>Reporte generado</title>
    
  </head>
  <!-- Los márgenes de la página se configuran de esa manera para evitar que el contenido se sobreponga al membrete -->
  <body>

    <!-- ENCABEZADO DE REPORTES -->
    <header>
      <div class="header" style="text-align: center;">
        <div class="row" style="height: 65px;">
        <img src="{{$logo_b64}}"  style="width: 40%; padding:20px; float: center;">
          <div style="margin-left:10%;">
            <h5>Instituto de pensiones del Estado</h5>
            <p style="font-size: 13px;font-weight: bold; margin:0; padding:0;">Subdirección Administrativa</p>
            <p style="font-size: 13px;font-weight: bold; margin:0; padding:0;">Almacén general</p>
          </div>
          <div style="margin-left: 75%;">
            <p style="font-size: 10px;font-weight: bold; margin:10; padding:0;">Fecha: {{$fecha}}</p>
            <p style="font-size: 10px; font-weight: bold;">Hora: {{$hora}}</p>
          </div>
        </div>
          <h4 style="word-wrap: break-word; width: 50%; margin-left: 27%; padding-bottom: 10px; ">{{$mensaje}}</h4>
      </div>
    </header>
    <div class="body">
      <table style="padding-top: 10px; margin-top: 0px; " class="table">
        @yield('content')
      </table>
    </div>
    <!-- TERMINA ENCABEZADO REPORTES -->


    <!-- ENCABEZADO DE PÓLIZAS -->
      @if($tipo == 'poliza')
      <div style="margin-top: 15%; font-size: 12px; text-align: center; display: block;">
          <table class="table" style="text-align: center;">
            <tr>
              <td>
                <input style="width: 250px; margin-left: 15%;" type="text" class="signature" />
              </td>
              <td>
                <input style="width: 250px; margin-left: 15%;" type="text" class="signature" />
              </td>
            </tr>
            <tr>
              <td>
                <p><b>ING. CRESENCIANO DOMINGUEZ SANCHEZ</b></p>
                <p>REVISÓ</p>
              </td>
              <td>
                <p><b>C.P. MANUEL GUZMÁN TRUJILLO</b></p>
                <P>AUTORIZÓ</P>
              </td>
            </tr>
          </table>
      </div>
      @endif
    <!-- TERMINA ENCABEZADO DE PÓLIZAS -->
  </body>

  <!-- PIE DE PÁGINA -->
  <footer>
  @if ($orientacion == "landscape")
    <script type="text/php">
        if(isset($pdf)){
            $pdf->page_script('
                if($PAGE_COUNT > 1){
                    $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                    $size = 10;
                    $y = 580;
                    $x = 700;
                    $pageText = "Página " . $PAGE_NUM . " de " . $PAGE_COUNT;               
                    $pdf->text($x, $y, $pageText, $font, $size);
                }
              ');
        }
    </script>
  @endif
  @if ($orientacion == "portrait")
  <script type="text/php">
        if(isset($pdf)){
            $pdf->page_script('
                if($PAGE_COUNT > 1){
                    $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                    $size = 10;
                    $y = 760;
                    $x = 520;
                    $pageText = "Página " . $PAGE_NUM . " de " . $PAGE_COUNT;               
                    $pdf->text($x, $y, $pageText, $font, $size);
                }
              ');
        }
  @endif
    </script>
  </footer>
<!-- TERMINA PIE DE PÁGINA -->

</html>