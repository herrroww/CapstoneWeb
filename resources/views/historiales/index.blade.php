@extends('layouts.sidebar')

@section('content')
<div class="container-fluid">
  <!-- SEARCH FORM -->
  <form class="form-inline ml-3 float-right">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" name="search" type="search" placeholder="Search"
                            aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>

<div class="container-fluid">
        <table class="table table-bordered" >
          <thead class="thead-dark">
            <tr>
             <th scope="col">Id</th>
              <th scope="col">Modelo</th>
              <th scope="col">Acción</th>
              <th scope="col">Usuario</th>
              <th scope="col">Fecha</th>
              <th scope="col">Valor Borrado</th>
              <th scope="col">Valor Agregado</th>
            </tr>
          </thead>
          <tbody id="audits">
            @foreach($audits as $audit)
              <tr>
              <td>{{ $audit->id }}</td>
              @if ( $audit->auditable_type == "App\Operario")
                <td>Operario (ID: {{ $audit->auditable_id}})</td>
                
                @elseif ( $audit->auditable_type == "App\Empresa")
                <td>Empresa (ID: {{ $audit->auditable_id}})</td>
                @elseif ($audit->auditable_type == "App\Asignar")
                <td> Asignar Componente (ID: {{ $audit->auditable_id }})</td>
                @elseif ($audit->auditable_type == "App\Componente")
                <td> Componente (ID: {{ $audit->auditable_id}})</td>
                @else
                <td>{{ $audit->auditable_type }}</td>
                @endif
              
                @if ( $audit->event == "created"  )
                <td>Creado </td>
                
                @elseif ( $audit->event == "deleted")
                <td>Eliminado</td>
                @elseif ( $audit->event == "updated" )
                <td>Editado  </td>
                @else
                <td>{{ $audit->event }}</td>
                @endif
                
                
                <td>{{ $audit->user->name }}</td>
                <td>{{ $audit->created_at }}</td>
                <td>
                  <table class="table-bordered">
                    @foreach($audit->old_values as $attribute => $value)
                      <tr>
                        <td><b>{{ $attribute }}</b></td>
                        <td>{{ $value }}</td>
                      </tr>
                    @endforeach
                  </table>
                </td>
                <td>
                  <table class="table-bordered">
                    @foreach($audit->new_values as $attribute => $value)
                      <tr>
                        <td><b>{{ $attribute }}</b></td>
                        <td>{{ $value }}</td>
                      </tr>
                    @endforeach
                  </table>
                </td>
              </tr>
            @endforeach
          </tbody>
    @if($search)
  <a href="{{ route('historialop') }}">
  <div style="position: absolute; left: 90%; bottom: 10%;">
  <button type="button" class="btn btn-secondary">Back</button>
 </div>
</a>
 @endif
        </table>
        {{ $audits->links()}}

      </div>
    @endsection