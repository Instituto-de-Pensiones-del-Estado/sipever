<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte generado</title>
  </head>
  <body>
    <style type="text/css" media="screen">
      @page { margin: 10px 25px; }
      header { 
        position: fixed; 
        top: 0px; 
        left: 0px; 
        right: 0px;
      }
      .hijo { 
        margin-top: 170px;
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
        border-top: 1px solid #dee2e6;
      }

      .table th{
        font-size: 9px;
      }

      .table thead th {
        vertical-align: bottom;
        border-bottom: 2px solid #dee2e6;
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
    </style>
    <header>
      <div class="header" style="text-align: center;">
        <div class="row" style="height: 6%;">
          <img src="{{$logo_b64}}"  style="width: 235px; padding:2px; float: center;">
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
      <table class="table">
        <tr>
          @foreach($headers as $header)
            <th>{{$header}}</th>
          @endforeach
        </tr>
      </table>
    </header>
    @yield('content')
  </body>
</html>