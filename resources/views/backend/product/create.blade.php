@extends('backend.layouts.master')

@section('main-content')

<div class="card">
  <h5 class="card-header">Add Product</h5>
  <div class="card-body">
    <form method="post" action="{{route('product.store')}}">
      {{csrf_field()}}
      <div class="row">
        <div class="col-md-4">
          <label for="gender">Gender <span class="text-danger">*</span></label>
          <select name="gender" id="gender" class="form-control">
            <option value="">--Select any gender--</option>
            <option value="male">Men</option>
            <option value="female">Women</option>
          </select>
        </div>
        <div class="col-md-8">
          <div class="form-group">
            <label for="cat_id">Category <span class="text-danger">*</span></label>
            <select name="cat_id" id="cat_id" class="form-control">
              <option value="">--Select any category--</option>
              @foreach($categories as $key=>$cat_data)
              <option value='{{$cat_data->id}}'>{{$cat_data->title}}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      <div class="form-group d-none" id="child_cat_div">
        <label for="child_cat_id">Sub Category</label>
        <select name="child_cat_id" id="child_cat_id" class="form-control">
          <option value="">--Select any category--</option>
          {{-- @foreach($parent_cats as $key=>$parent_cat)
                  <option value='{{$parent_cat->id}}'>{{$parent_cat->title}}</option>
          @endforeach --}}
        </select>
      </div>
      <div class="form-group">
        <label for="inputTitle" class="col-form-label">Article Title <span class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="title" placeholder="Enter title" value="{{old('title')}}" class="form-control">
        @error('title')
        <span class="text-danger">{{$message}}</span>
        @enderror
      </div>

      <div class="form-group">
        <label for="is_featured">Is Featured</label><br>
        <input type="checkbox" name='is_featured' id='is_featured' value='1' checked> Yes
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="price" class="col-form-label">Price(NRS) <span class="text-danger">*</span></label>
            <input id="price" type="number" name="price" placeholder="Enter price" value="{{old('price')}}" class="form-control">
            @error('price')
            <span class="text-danger">{{$message}}</span>
            @enderror
          </div>
        </div>
     
        <div class="col-md-6">
          <div class="form-group">
            <label for="discount" class="col-form-label">Discount(%)</label>
            <input id="discount" type="number" name="discount" min="0" max="100" placeholder="Enter discount" value="{{old('discount')}}" class="form-control">
            @error('discount')
            <span class="text-danger">{{$message}}</span>
            @enderror
          </div>
        </div>
      </div>

      <div class="row mt-3">
        <div class="col-md-6">
          <div class="form-group">
            <label for="size">Size</label>
            <select name="size[]" class="form-control selectpicker" multiple data-live-search="true">
              <option value="">--Select any size--</option>
              <option value="S">Small (S)</option>
              <option value="M">Medium (M)</option>
              <option value="L">Large (L)</option>
              <option value="XL">Extra Large (XL)</option>
            </select>
          </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
            <label for="colors">Colors</label>
            <select name="colors[]" class="form-control selectpicker" multiple data-live-search="true">
              <option value="">--Select any color--</option>
              @foreach($brands as $brand)
                <option value="{{$brand->id}}">{{$brand->title}}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="stock">Quantity <span class="text-danger">*</span></label>
        <input id="quantity" type="number" name="stock" min="0" placeholder="Enter quantity" value="{{old('stock')}}" class="form-control">
        @error('stock')
        <span class="text-danger">{{$message}}</span>
        @enderror
      </div>
      <div class="form-group">
        <label class="col-form-label">
            Photos <span class="text-danger">*</span>
        </label>

        <div id="product-image-dropzone" class="dropzone border rounded p-2">
        </div>

        @error('photo')
            <span class="text-danger">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-control">
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
        @error('status')
        <span class="text-danger">{{$message}}</span>
        @enderror
      </div>

            <div class="form-group">
        <label for="summary" class="col-form-label">Summary <span class="text-danger">*</span></label>
        <textarea class="form-control" id="summary" name="summary">{{old('summary')}}</textarea>
        @error('summary')
        <span class="text-danger">{{$message}}</span>
        @enderror
      </div>

      <div class="form-group">
        <label for="description" class="col-form-label">Description</label>
        <textarea class="form-control" id="description" name="description">{{old('description')}}</textarea>
        @error('description')
        <span class="text-danger">{{$message}}</span>
        @enderror
      </div>
      
      <div id="temp-paths"></div> 
      <div class="form-group mb-3">
        <button type="reset" class="btn btn-warning">Reset</button>
        <button class="btn btn-success" type="submit">Submit</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
@endpush
@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>

<script>
  

  $(document).ready(function() {
    $('#summary').summernote({
      placeholder: "Write short description.....",
      tabsize: 2,
      height: 100
    });
  });

  $(document).ready(function() {
    $('#description').summernote({
      placeholder: "Write detail description.....",
      tabsize: 2,
      height: 150
    });
  });
  // $('select').selectpicker();
</script>

<script>
  $('#cat_id').change(function() {
    var cat_id = $(this).val();
    // alert(cat_id);
    if (cat_id != null) {
      // Ajax call
      $.ajax({
        url: "/admin/category/" + cat_id + "/child",
        data: {
          _token: "{{csrf_token()}}",
          id: cat_id
        },
        type: "POST",
        success: function(response) {
          if (typeof(response) != 'object') {
            response = $.parseJSON(response)
          }
          // console.log(response);
          var html_option = "<option value=''>----Select sub category----</option>"
          if (response.status) {
            var data = response.data;
            // alert(data);
            if (response.data) {
              $('#child_cat_div').removeClass('d-none');
              $.each(data, function(id, title) {
                html_option += "<option value='" + id + "'>" + title + "</option>"
              });
            } else {}
          } else {
            $('#child_cat_div').addClass('d-none');
          }
          $('#child_cat_id').html(html_option);
        }
      });
    } else {}
  })

   $('#gender').change(function() {
    var gender = $(this).val();
    // alert(gender);
    if (gender != null) {
      // Ajax call
      $.ajax({
        url: "/admin/category/parent",
        data: {
          _token: "{{csrf_token()}}",
          gender: gender
        },
        type: "POST",
        success: function(response) {
          if (typeof(response) != 'object') {
            response = $.parseJSON(response)
          }
          // console.log(response);
          var _parent = "<option value=''>--Select category--</option>"
          if (response.status) {
            var data = response.data;
            if (response.data && data.length > 0) {
              $('#cat_div').removeClass('d-none');
            $.each(data, function(index, item) {
                _parent += "<option value='" + item.id + "'>" + item.title + "</option>";
            });
            } else {
                _parent += "<option value=''>No category found</option>";
            }
          } else {
             _parent += "<option value=''>No category found</option>";
          }
          $('#cat_id').html(_parent);
        }
      });
    } else {}
  })
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
<script>
Dropzone.autoDiscover = false;

const myDropzone = new Dropzone("#product-image-dropzone", {
    url: "/admin/products/images/temp", 
    paramName: "file",
    maxFilesize: 2,
    acceptedFiles: "image/*",
    addRemoveLinks: true,
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    init: function () {
        @isset($product)
            @foreach($product->images as $image)
                let mockFile{{ $image->id }} = { name: "image-{{ $image->id }}", size: 1234, id: {{ $image->id }} };
                this.displayExistingFile(mockFile{{ $image->id }}, "{{ $image->url }}");
            @endforeach
        @endisset

        this.on("removedfile", function (file) {
            if (file.id) {
                fetch(`/products/images/${file.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });
            }
        });

        this.on("success", function (file, response) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'temp_images[]';
            input.value = response.temp_path;
            document.getElementById('temp-paths').appendChild(input);
        });
    }
});
</script>
@endpush