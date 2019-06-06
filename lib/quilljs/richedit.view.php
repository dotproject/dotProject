
<div style="background-color: #fff; border: 1px solid #000;">
  <div id="<?php echo $this->id ?>-toolbar-container">
    <span class="ql-formats">
      <select class="ql-font"></select>
      <select class="ql-size"></select>
      <select class="ql-align"></select>
    </span>
    <span class="ql-formats">
      <button class="ql-bold"></button>
      <button class="ql-italic"></button>
      <button class="ql-underline"></button>
      <button class="ql-strike"></button>
    </span>
    <span class="ql-formats">
      <button class="ql-header" value="1"></button>
      <button class="ql-header" value="2"></button>
    </span>
    <span class="ql-formats">
      <button class="ql-list" value="ordered"></button>
      <button class="ql-list" value="bullet"></button>
      <button class="ql-link"></button>
    </span>
    <span class="ql-formats">
      <button class="ql-clean"></button>
    </span>
  </div>
  <div id="<?php echo $this->id ?>-editor-container"><?php echo $this->content ?></div>
</div>

<textarea id="<?php echo $this->id ?>" name="<?php echo $this->id ?>" style="width:1px;height:1px;visibility:hidden;"><?php echo $this->content ?></textarea>

<script>
var quill = new Quill('#<?php echo $this->id ?>-editor-container', {
    modules: {
        toolbar: '#<?php echo $this->id ?>-toolbar-container'
    },
    placeholder: '',
    theme: 'snow'
});
quill.on('text-change', function() {
  var textarea = document.getElementById('<?php echo $this->id ?>');
  textarea.value = quill.root.innerHTML;
});
</script>