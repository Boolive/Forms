<div class="form form_auto">
    <h2 class="form__title"><?=$v['object']->entity()->proto(null, true)->title?></h2>
    <?php
    $result = $v['result']->int();
    $result_class = $result==1 ? 'form__result_error' : ($result==2 ? 'form__result_ok' : '');
    if ($result_class):
    ?>
    <div class="form__result <?=$result_class?>">
        <?php echo $v['message']->string(); ?>
    </div>
    <?php endif; ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <!-- Идентификатор формы -->
        <input type="hidden" name="form" value="<?=$v['controller']?>">
        <input type="hidden" name="object" value="<?=$v['object']?>">
        <!-- Поля -->
        <?php
        $list = $v['views']->arrays(\boolive\core\values\Rule::string());
        foreach ($list as $item){
            echo $item;
        }
        ?>
<!--        <input type="file" name="title">-->
        <div class="form__buttons">
            <input class="btn btn_type_primary" type="submit" name="<?=$v['controller']?>[submit]" value="Сохранить">
        </div>
    </form>
</div>