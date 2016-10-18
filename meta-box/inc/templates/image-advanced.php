<script id="tmpl-swpmb-image-item" type="text/html">
  <input type="hidden" name="{{{ data.fieldName }}}" value="{{{ data.id }}}" class="swpmb-media-input">
  <div class="swpmb-media-preview">
    <div class="swpmb-media-content">
        <# if ( 'image' === data.type && data.sizes ) { #>
          <# if ( data.sizes.thumbnail ) { #>
            <img src="{{{ data.sizes.full.url }}}">
          <# } else { #>
            <img src="{{{ data.sizes.full.url }}}">
          <# } #>
        <# } else { #>
          <# if ( data.image && data.image.src && data.image.src !== data.icon ) { #>
            <img src="{{ data.image.src }}" />
          <# } else { #>
            <img src="{{ data.icon }}" />
          <# } #>
        <# } #>
     
    </div>
  </div>
  <div class="swpmb-overlay"></div>
  <div class="swpmb-media-bar">
    <a class="swpmb-edit-media" title="{{{ i18nSwpmbMedia.edit }}}" href="{{{ data.editLink }}}" target="_blank">
      <span class="dashicons dashicons-edit"></span>
    </a>
    <a href="#" class="swpmb-remove-media" title="{{{ i18nSwpmbMedia.remove }}}">
      <span class="dashicons dashicons-no-alt"></span>
    </a>
  </div>
</script>
