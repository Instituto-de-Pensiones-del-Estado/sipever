<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte generado</title>
  </head>
  <body>
    <style type="text/css" media="screen">
      @page { margin: 10px 25px; }
      body{
        font-family: Panton;
        src:url('{{public_path('fonts/Panton-Regular.otf')}}');
      }
      header {
        position: fixed;
        top: 0px;
        left: 0px;
        right: 0px;
      }
      .hijo {
        margin-top: 20%;
        page-break-after: always;
      }
      .hijo:last-child { page-break-after: never; }
      .header{
          border-bottom-style: solid;
          border-bottom-color: #A9ADCC;
      }
      .table {
        width: 100%;
        margin-bottom: 1rem;
        background-color: transparent;
      }
      .table th,
      .table td {
        padding: 0.75rem;
        vertical-align: top;
      }
      .table th{
        font-size: 11px;
      }
      .table tbody td{
        font-size: 11px;
      }
      .table thead{
         border-bottom: 2px solid #dee2e6;
      }
      .table thead th {
        vertical-align: bottom;
      }
      .table tbody + tbody {
        border-top: 2px solid #dee2e6;
      }
      .table .table {
        background-color: #fff;
      }
      .row {
        display: -ms-flexbox;
        display: inline-flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
      }
      .signature {
        border: 0;
        border-bottom: 1px solid #000;
      }
    </style>
    <header>
      <div class="header" style="text-align: center;">
        <div class="row" style="height: 65px;">
        <img src="{{$logo_b64}}"  style="width: 40%; padding:2px; float: center;">
          <div style="margin-left:10%;">
            <h5>Instituto de pensiones del Estado</h5>
            <p style="font-size: 13px;font-weight: bold; margin:0; padding:0;">Subdirección Administrativa</p>
            <p style="font-size: 13px;font-weight: bold; margin:0; padding:0;">Almacén general</p>
          </div>
          <div style="margin-left: 75%;">
            <p style="font-size: 10px;font-weight: bold; margin:0; padding:0;">Fecha: {{$fecha}}</p>
            <p style="font-size: 10px; font-weight: bold;">Hora: {{$hora}}</p>
          </div>
        </div>
          <h4 style="word-wrap: break-word; width: 50%; margin-left: 27%; padding:10px; ">{{$mensaje}}</h4>
      </div>
    </header>
    <div class="hijo">
      <table class="table">
      <thead>
        <tr>
          @foreach($headers as $header)
              <th style="white-space: nowrap;">{{$header}}</th>
          @endforeach
        </tr>
      </thead>
        @yield('content')
      </table>
    </div>
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
  </body>
</html>