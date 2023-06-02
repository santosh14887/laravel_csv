@extends('layouts.after_login')

@section('content')  
@if (Session::has('message'))
    <div class="alert alert-success">{{ Session::get('message') }}</div>
@endif         
            <div class="panel panel-primary">        
                <div class="panel-heading">
                    <h2>Upload Csv</h2>
                </div>        
                <div class="panel-body">                
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-block">
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif
                
                    <form action="{{ route('file.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf            
                        <div class="mb-3">
                            <label class="form-label">Select Files:</label>
                            <input type="file" name="csv_files[]" multiple class="form-control @error('csv_files') is-invalid @enderror" accept=".csv">
            
                            @error('csv_files')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>            
                        <div class="mb-3">
                            <button type="submit" class="btn btn-success">Upload</button>
                        </div>                
                    </form>                
                </div>
            </div>
        @endsection