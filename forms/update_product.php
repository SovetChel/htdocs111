<form enctype="multipart/form-data" action="/admin.php/<?php echo $table_name ?>/update/" method="post">
    <h1>Изменение товара</h1>
    <div>
        <input type="hidden" name="id" value="<?php echo $data["id"] ?? "" ?>">
        <input type="text" name="name" value="<?php echo $data["name"] ?? "" ?>" placeholder="Название" required>
        <textarea name="description" cols="30" rows="5" placeholder="Описание" required><?php echo $data["description"] ?? "" ?></textarea>
        <input type="number" name="price" value="<?php echo $data["price"] ?? "" ?>" placeholder="Цена" required>
        <div class="radio-container">
            <label>Категория</label>
            <?php
                $category_id = $data["category_id"] ?? -1;
                foreach($categories as $category) {
                    $checked = "";
                    if ($category["id"] == $category_id) {
                        $checked = "checked";
                    };
                    echo <<< EOT
                    <label><input type="radio" {$checked} value="{$category['id']}" name="category_id"><span>{$category['name']}</span></label>
                    EOT;
                };
            ?>
            <a href="/admin.php/category">Добавить категорию</a>
        </div>
        <div>
            <label for="preview">Изображение товара</label>
            <input type="file" name="preview" id="preview" accept="image/*">
        </div>
        <input type="submit" value="Изменить">
    </div>
    <a href="/admin.php/<?php echo $table_name ?>/remove?id=<?php echo $data['id'] ?? "" ?>" class="remove-link">Удалить</a>
</form>