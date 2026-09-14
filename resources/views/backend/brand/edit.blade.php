@extends('backend.layouts.master')
@section('title','E-SHOP || Color Edit')
@section('main-content')

<div class="card">
    <h5 class="card-header">Edit color</h5>
    <div class="card-body">
      <form method="post" action="{{route('brand.update',$brand->id)}}">
        @csrf 
        @method('PATCH')
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">Color Name <span class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="title" placeholder="Enter color name"  value="{{$brand->title}}" class="form-control">
        @error('title')
        <span class="text-danger">{{$message}}</span>
        @enderror
        </div>        
        <div class="form-group">
          <label for="color_code" class="col-form-label">Color Code <span class="text-danger">*</span></label>
          <div class="d-flex align-items-center">
            <input id="color_code" type="color" name="color_code" value="{{old('color_code', $brand->color_code)}}" class="mr-2" style="width:56px;height:38px;padding:2px">
            <input id="color_code_text" type="text" value="{{old('color_code', $brand->color_code)}}" class="form-control" maxlength="7" pattern="^#[0-9A-Fa-f]{6}$">
          </div>
          @error('color_code')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group">
          <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
          <select name="status" class="form-control">
            <option value="active" {{(($brand->status=='active') ? 'selected' : '')}}>Active</option>
            <option value="inactive" {{(($brand->status=='inactive') ? 'selected' : '')}}>Inactive</option>
          </select>
          @error('status')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group mb-3">
           <button class="btn btn-success" type="submit">Update</button>
        </div>
      </form>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
@endpush
@push('scripts')
<script>
  $('#color_code').on('input', function () { $('#color_code_text').val(this.value); });
  $('#color_code_text').on('input', function () { if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) $('#color_code').val(this.value); });
</script>
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
<script>
    $('#lfm').filemanager('image');

    $(document).ready(function() {
    $('#description').summernote({
      placeholder: "Write short description.....",
        tabsize: 2,
        height: 150
    });
    });
</script>
@endpush