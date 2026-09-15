@extends('backend.layouts.master')

@section('main-content')

<div class="card">
  <h5 class="card-header">Add Product</h5>
  <div class="card-body">
    <form method="post" action="{{route('product.store')}}" enctype="multipart/form-data">
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

      <div class="form-group">
        <label class="col-form-label">Product Section</label>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="section" id="section_focus" value="focus" {{ old('section') == 'focus' ? 'checked' : '' }}>
          <label class="form-check-label" for="section_focus">Categories in Focus</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="section" id="section_must_haves" value="must_haves" {{ old('section') == 'must_haves' ? 'checked' : '' }}>
          <label class="form-check-label" for="section_must_haves">Must-Haves</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="section" id="section_sale_essentials" value="sale_essentials" {{ old('section') == 'sale_essentials' ? 'checked' : '' }}>
          <label class="form-check-label" for="section_sale_essentials">Sale essentials</label>
        </div>
        <small class="form-text text-muted">If no section is selected, the product will be added to the common section.</small>
        @error('section')
        <span class="text-danger">{{$message}}</span>
        @enderror
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

      <div class="form-group">
        <label class="col-form-label">Product Variants</label>
        <div class="border rounded p-3 bg-light">
          <div id="variant-rows">
            <div class="row variant-row mb-2 align-items-end">
              <div class="col-md-3">
                <label>Size</label>
                <select name="variants[0][size]" class="form-control">
                  <option value="">--Select size--</option>
                  <option value="S">Small</option>
                  <option value="M">Medium</option>
                  <option value="L">Large</option>
                  <option value="XL">Extra Large</option>
                </select>
              </div>
              <div class="col-md-3">
                <label>Color</label>
                <select name="variants[0][color]" class="form-control">
                  <option value="">--Select color--</option>
                  @foreach($brands as $brand)
                    <option value="{{$brand->title}}" data-color-code="{{$brand->color_code}}">{{$brand->title}}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <label>Quantity</label>
                <input type="number" name="variants[0][quantity]" class="form-control" min="0" placeholder="e.g. 5">
              </div>
              <div class="col-md-2">
                <label>Image</label>
                <input type="file" class="form-control-file variant-image-input" accept="image/*">
                <input type="hidden" name="variants[0][image]" class="variant-image-path">
                <img class="variant-image-preview rounded-circle d-none mt-1" width="48" height="48" alt="Variant preview">
              </div>
              <input type="hidden" name="variants[0][color_code]" class="variant-color-code" value="#000000">
              <span class="variant-color-preview rounded-circle ml-2 mb-1" style="width:24px;height:24px;background:#000000"></span>
              <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger btn-sm remove-variant" disabled>Remove</button>
              </div>
            </div>
          </div>
          <button type="button" id="add-variant" class="btn btn-outline-primary btn-sm mt-2">Add Variant</button>
        </div>
      </div>

      <div class="form-group">
        <label for="stock">Default Quantity <span class="text-danger">*</span></label>
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
        <label for="size_chart" class="col-form-label">Size Chart</label>
        <input id="size_chart" type="file" name="size_chart" accept="image/jpeg,image/png,image/webp" class="form-control-file">
        <small class="form-text text-muted">Upload one JPG, PNG, or WebP image (maximum 4 MB).</small>
        @error('size_chart')
        <span class="text-danger">{{$message}}</span>
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
      
      <div id="temp-paths">
        <input type="hidden" name="primary_image" id="primary-image-path">
      </div>
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
  let variantIndex = 1;

  $('#add-variant').on('click', function () {
    const newRow = `
      <div class="row variant-row mb-2 align-items-end">
        <div class="col-md-3">
          <label>Size</label>
          <select name="variants[${variantIndex}][size]" class="form-control">
            <option value="">--Select size--</option>
            <option value="S">Small</option>
            <option value="M">Medium</option>
            <option value="L">Large</option>
            <option value="XL">Extra Large</option>
          </select>
        </div>
        <div class="col-md-3">
          <label>Color</label>
          <select name="variants[${variantIndex}][color]" class="form-control">
            <option value="">--Select color--</option>
            @foreach($brands as $brand)
              <option value="{{$brand->title}}" data-color-code="{{$brand->color_code}}">{{$brand->title}}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label>Quantity</label>
          <input type="number" name="variants[${variantIndex}][quantity]" class="form-control" min="0" placeholder="e.g. 5">
        </div>
        <div class="col-md-2">
          <label>Image</label>
          <input type="file" class="form-control-file variant-image-input" accept="image/*">
          <input type="hidden" name="variants[${variantIndex}][image]" class="variant-image-path">
          <img class="variant-image-preview rounded-circle d-none mt-1" width="48" height="48" alt="Variant preview">
        </div>
        <input type="hidden" name="variants[${variantIndex}][color_code]" class="variant-color-code" value="#000000">
        <span class="variant-color-preview rounded-circle ml-2 mb-1" style="width:24px;height:24px;background:#000000"></span>
        <div class="col-md-2">
          <button type="button" class="btn btn-outline-danger btn-sm remove-variant">Remove</button>
        </div>
      </div>`;

    $('#variant-rows').append(newRow);
    variantIndex++;
  });

  $(document).on('click', '.remove-variant', function () {
    const rows = $('.variant-row');
    if (rows.length > 1) {
      $(this).closest('.variant-row').remove();
    }
  });

  $(document).on('change', '.variant-image-input', function () {
    const input = this;
    const row = $(input).closest('.variant-row');
    const preview = row.find('.variant-image-preview');
    const path = row.find('.variant-image-path');
    const file = input.files[0];

    if (!file) return;

    preview.attr('src', URL.createObjectURL(file)).removeClass('d-none');
    const formData = new FormData();
    formData.append('file', file);

    $.ajax({
      url: '/admin/products/images/temp',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      success: function (response) { path.val(response.temp_path); }
    });
  });

  $(document).on('change', 'select[name*="[color]"]', function () {
    const row = $(this).closest('.variant-row');
    const colorCode = $(this).find(':selected').data('color-code') || '#000000';
    row.find('.variant-color-code').val(colorCode);
    row.find('.variant-color-preview').css('background-color', colorCode);
  });

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
            file.tempPath = response.temp_path;
            file.previewElement.addEventListener('click', function () {
              document.getElementById('primary-image-path').value = file.tempPath;
              document.querySelectorAll('#product-image-dropzone .dz-preview').forEach(function (preview) {
                preview.classList.remove('border', 'border-primary');
              });
              file.previewElement.classList.add('border', 'border-primary');
            });
        });
    }
});
</script>
@endpush