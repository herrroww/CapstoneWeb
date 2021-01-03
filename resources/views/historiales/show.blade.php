@extends('layouts.sidebar')

@section('content')

<div class="container">
    <div class="col-12 pt-3 pb-3 text-center" >
        <h2>Cambios Realizados</h2>
    </div>
    <hr>  
    
<div class="container">
        <table class="table table-bordered " >
          <thead >
            <tr>
            @if($audit->old_values!=null && $audit->new_values==null)
             <th scope="col" class=" text-center bg-blue color-white">Valor Borrado</th>
              
              @elseif($audit->old_values==null && $audit->new_values!=null)
              <th scope="col" class=" text-center bg-blue color-white">Valor Agregado</th>
              
              @else
              <th scope="col" class="text-center bg-blue color-white">Valor Borrado</th>
              <th scope="col" class="text-center bg-blue color-white">Valor Agregado</th>
              @endif
            </tr>
          </thead>
          
          <tbody id="audits" >
            <tr>
                <td>
                <table class="table-bordered ">
                
                @if($audit->old_values!=null && $audit->new_values==null)
                </table>
                <table class="table ">
                    @foreach($audit->old_values as $attribute => $value)
                    @if($attribute!="contraseniaOperario" && $attribute!="contraseniaOperarioFTP" && $value!="" && $attribute!="")
                      <tr>
                        <td><b>{{ $attribute }}</b></td>
                        <td>{{ $value }}</td>
                      </tr>
                    @endif
                    @endforeach             
                </td>

                
                <td>
                @elseif($audit->old_values==null && $audit->new_values!=null)
                </table>
                <table class="table ">
                  
                    @foreach($audit->new_values as $attribute => $value)
                    @if($attribute!="contraseniaOperario" && $attribute!="contraseniaOperarioFTP" && $value!=""  && $attribute!="")
                      <tr>
                        <td><b>{{ $attribute }}</b></td>
                        <td>{{ $value }}</td>
                      </tr>
                      @endif
                    @endforeach
                  
                </td>
                @else
                  <table class="table">
                    @foreach($audit->old_values as $attribute => $value)
                    @if($attribute!="contraseniaOperario" && $attribute!="contraseniaOperarioFTP" && $value!="")
                      <tr>
                        <td><b>{{ $attribute }}</b></td>
                        <td>{{ $value }}</td>
                      </tr>
                      @endif
                    @endforeach
                  </table>
                </td>
                <td>
                  <table class="table">
                    @foreach($audit->new_values as $attribute => $value)
                    @if($attribute!="contraseniaOperario" && $attribute!="contraseniaOperarioFTP" && $value!="")
                      <tr>
                        <td><b>{{ $attribute }}</b></td>
                        <td>{{ $value }}</td>
                      </tr>
                      @endif
                    @endforeach
                  </table>
                </td>             
              </tr>           
          </tbody>
          @endif
        </table>
    
          <a href="{{ url()->previous() }}">
  <div style="position: absolute; left: 90%; bottom: 10%;">
  <button type="button" class="btn btn-secondary">Volver</button>
 </div>
</a>

</table>
       

      </div>
    @endsection