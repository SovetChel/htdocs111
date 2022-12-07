<div id="confirm-purchase" <?php echo $product["id"] ?>>
    <p>Подтверждение покупки<button id="close-confirm-purchase"></button></p>
    <form action="" method="POST">
        <input type="hidden" name="id" value="<?php echo $product["id"] ?>">
        <p>Товар: <?php echo $product["name"] ?></p>
        <p>Цена: <?php echo $product["price"] ?> р.</p>
        <input type="submit" value="Купить" disabled>
    </form>
</div>