<?php
$product = $this->viewData['product'];
$image = $this->viewData['image'];
$details = $this->viewData['details'];
?>

<section>
    <article class="product-container">
        <aside>
            <figure>
                <?php echo $image; ?>
            </figure>
        </aside>
        <div class="product-details">
            <h1><?php echo $product->name; ?></h1>
            <p style="width: 100%;"><strong>€ <?php echo $product->price ?></strong></p>

            <?php echo $details; ?>
            
        </div>

    </article>
</section>