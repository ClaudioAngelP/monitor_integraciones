@extends('prueba')
@section('content')
<div class="container w-25 border p-4 mt-4">
    <form>
        <div class="mb-3">
          <label for="title" class="form-label">Titulo de la tarea</label>
          <input type="text" class="form-control" name="title" >
        </div>
        <div class="align-center">
            <button type="submit" class="btn btn-primary">Crear nueva tarea</button>
        </div>
      </form>
</div>
@endsection
