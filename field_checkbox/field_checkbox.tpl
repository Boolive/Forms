<div class="field field_checkbox<?=$v['error']->bool()?' field_state_error':''?>">
    <input type="hidden" name="<?=$v['uri']?>" value="0">
    <input type="checkbox" name="<?=$v['uri']?>" value="1" <?=$v['value']->bool()?"checked":""?> id="id_<?=$v['id']?>">
    <label class="field__label" for="id_<?=$v['id']?>"><?=$v['title']?></label>
    <?php if ($v['error']->bool()): ?>
    <div class="field__error"><?=$v['error']?></div>
    <?php endif; ?>
</div>