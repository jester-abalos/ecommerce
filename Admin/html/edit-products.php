<?php
require '../../connection/connection.php';

$collectionproducts = $client->GADGETHUB->products;
$colected = $collectionproducts->find([])->toArray();
$categories = [];
foreach ($colected as $product) {
    $categories[] = $product['productCategory'];
}
$categories = array_unique($categories);

$imageData = [];
foreach ($colected as $product) {
    if (isset($product['image'])) {
        $imagePath = _DIR_ . '/../imageadmin/' . $product['image'];
        if (file_exists($imagePath)) {
            $imageData[$product['productName']] = file_get_contents($imagePath);
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productName = $_POST['productNameInput'];
    $productCount = 0;
    $productPrice = $_POST['productPriceInput'];
    $productCategory = $_POST['productCategoryInput'];
    $productStock = $_POST['productStockInput'];
    $productSize = $_POST['productSizeInput'];
    $productType = $_POST['productTypeInput'];
    $productSkin = $_POST['productSkinInput'];
    $productBenefit = $_POST['productBenefitInput'];
    $productMaining = $_POST['productMainingInput'];
    $productIng = $_POST['productIngInput'];
    $productDesc = $_POST['productDescInput'];
    $image = $_FILES['image'];
    $imageName = $image['name'];
    $imageTmpName = $image['tmp_name'];
    $imageSize = $image['size'];
    $imageError = $image['error'];
    $imageType = $image['type'];
    $imageExt = explode('.', $imageName);
    $imageActualExt = strtolower(end($imageExt));
    $allowed = array('jpg', 'jpeg', 'png', 'gif');

    if (in_array($imageActualExt, $allowed)) {
        if ($imageError === 0) {
            if ($imageSize < 5000000) {
                $imageNameNew = uniqid('', true) . "." . $imageActualExt;
                $destination = _DIR_ . "/../../allasset/" . $imageNameNew;
                if (move_uploaded_file($imageTmpName, $destination)) {
                    if ($collectionproducts->insertOne([
                        'productName' => $productName,
                        'productDescription' => $productDescription,
                        'productCategory' => $productCategory,
                        'productStock' => $productBrand,
                        'productPrice' => $productPrice,
                        'productStockQuantity' => $productStockQuantity,
                        'image' => $imageNameNew,
                        'productCount' => $productCount

                    ])) {
                        echo "<script>alert('Product added successfully!');</script>";
                        echo "<script>window.location.href = 'products.php';</script>";
                        exit;
                    }
                } else {
                    echo "<script>alert('Error uploading image');</script>";
                }
            } else {
                echo "<script>alert('Image size should be less than 5mb');</script>";
            }
        } else {
            echo "<script>alert('Error uploading image');</script>";
        }
    } else {
        echo "<script>alert('Image type should be jpg, jpeg, png or gif');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Document</title>
</head>
<body>

    <!-- PRODUCT INFO-->
    <form action="" method="post" enctype="multipart/form-data">
    <div class="user-info">
        <div class="box1"></div>
        <div class="box2">
            <img src="" alt="" id="output" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <div class="file">
            <input type="file" name="image" id="file" onchange="loadFile(event)">
            <button name="submit" type="submit">Upload</button>
        </div>
        <div class="text">
            <textarea id="productNameInput" name="productNameInput" maxlength="1000" onfocus="clearDefaultValue(this)" onblur="restoreDefaultValue(this)">Product Name:</textarea>
            <textarea id="productPriceInput" name="productPriceInput" maxlength="1000" onfocus="clearDefaultValue(this)" onblur="restoreDefaultValue(this)">Product Price:</textarea>
            <textarea id="productCategoryInput" name="productCategoryInput" maxlength="1000" onfocus="clearDefaultValue(this)" onblur="restoreDefaultValue(this)">Category:</textarea>
            <textarea id="productStockInput" name="productStockInput" maxlength="1000" onfocus="clearDefaultValue(this)" onblur="restoreDefaultValue(this)">Stock:</textarea>
            <textarea id="productSizeInput" name="productSizeInput" maxlength="1000" onfocus="clearDefaultValue(this)" onblur="restoreDefaultValue(this)">Size:</textarea>
            <textarea id="productTypeInput" name="productTypeInput" maxlength="1000" onfocus="clearDefaultValue(this)" onblur="restoreDefaultValue(this)">Type:</textarea>
            <textarea id="productSkinInput" name="productSkinInput" maxlength="1000" onfocus="clearDefaultValue(this)" onblur="restoreDefaultValue(this)">Skin Concern:</textarea>
            <textarea id="productBenefitInput" name="productBenefitInput" maxlength="1000" onfocus="clearDefaultValue(this)" onblur="restoreDefaultValue(this)">Benefits:</textarea>
            <textarea id="productMainingInput" name="productMainingInput" maxlength="1000" onfocus="clearDefaultValue(this)" onblur="restoreDefaultValue(this)">Main Ingredients:</textarea>
            <textarea id="productIngInput" name="productIngInput" maxlength="1000" onfocus="clearDefaultValue(this)" onblur="restoreDefaultValue(this)">Ingredients:</textarea>
            <textarea id="productDescInput" name="productDescInput" maxlength="1000" onfocus="clearDefaultValue(this)" onblur="restoreDefaultValue(this)">Describe your product..</textarea>
        </div>
    </div>
</form>

</body>
</html>
    <script src="../adminjs/products.js"> </script>
<!--matic mag a-appear yung chosen image-->
    <script src="../adminjs/chosenappear.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN6jIeHz" crossorigin="anonymous"></script>
    
</body>
</html>














<?php
require '../../connection/connection.php';

$collectionproducts = $client->GADGETHUB->products;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $productName = $_POST['productNameInput'];
    $productDescription = $_POST['productDescInput'];  // Ensure product description is included
    $productCategory = $_POST['productCategoryInput'];
    $productPrice = $_POST['productPriceInput'];
    $productStock = $_POST['productStockInput'];  // This is the stock quantity field
    $productStockQuantity = $productStock; // Assign productStockQuantity to the value of productStock
    $productSize = $_POST['productSizeInput'];
    $productType = $_POST['productTypeInput'];
    $productSkin = $_POST['productSkinInput'];
    $productBenefit = $_POST['productBenefitInput'];
    $productMaining = $_POST['productMainingInput'];
    $productIng = $_POST['productIngInput'];
    $productCount = 0; // Default product count, can be adjusted later

    // Handle image upload
    $image = $_FILES['image'];
    if ($image && isset($image['name'])) {
        $imageName = $image['name'];
        $imageTmpName = $image['tmp_name'];
        $imageSize = $image['size'];
        $imageError = $image['error'];
        $imageExt = explode('.', $imageName);
        $imageActualExt = strtolower(end($imageExt));
        $allowed = array('jpg', 'jpeg', 'png', 'gif');

        if (in_array($imageActualExt, $allowed)) {
            if ($imageError === 0) {
                if ($imageSize < 5000000) {
                    $imageNameNew = uniqid('', true) . "." . $imageActualExt;
                    $destination = __DIR__ . "/../../allasset/" . $imageNameNew;
                    if (move_uploaded_file($imageTmpName, $destination)) {
                        // Insert the product data into MongoDB
                        $insertResult = $collectionproducts->insertOne([
                            'productName' => $productName,
                            'productDescription' => $productDescription,
                            'productCategory' => $productCategory,
                            'productStock' => $productStock,  // Used as productBrand in your reference
                            'productPrice' => $productPrice,
                            'productStockQuantity' => $productStockQuantity,  // Updated stock field
                            'image' => $imageNameNew,
                            'productCount' => $productCount
                        ]);

                        if ($insertResult) {
                            echo "<script>alert('Product added successfully!');</script>";
                            echo "<script>window.location.href = 'products.php';</script>";
                            exit;
                        } else {
                            echo "<script>alert('Error inserting product into database.');</script>";
                        }
                    } else {
                        echo "<script>alert('Error uploading image');</script>";
                    }
                } else {
                    echo "<script>alert('Image size should be less than 5MB');</script>";
                }
            } else {
                echo "<script>alert('Error uploading image');</script>";
            }
        } else {
            echo "<script>alert('Invalid image type. Only jpg, jpeg, png, and gif are allowed');</script>";
        }
    } else {
        echo "<script>alert('Please upload an image');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Add Product</title>
</head>
<body>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="user-info">
            <div class="box1"></div>
            <div class="box2">
                <img src="" alt="" id="output" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div class="file">
                <input type="file" name="image" id="file" onchange="loadFile(event)">
                <button name="submit" type="submit">Upload</button>
            </div>
            <div class="text">
                <textarea id="productNameInput" name="productNameInput" placeholder="Product Name"></textarea>
                <textarea id="productPriceInput" name="productPriceInput" placeholder="Product Price"></textarea>
                <textarea id="productCategoryInput" name="productCategoryInput" placeholder="Category"></textarea>
                <textarea id="productStockInput" name="productStockInput" placeholder="Stock Quantity"></textarea>
                <textarea id="productSizeInput" name="productSizeInput" placeholder="Size"></textarea>
                <textarea id="productTypeInput" name="productTypeInput" placeholder="Type"></textarea>
                <textarea id="productSkinInput" name="productSkinInput" placeholder="Skin Concern"></textarea>
                <textarea id="productBenefitInput" name="productBenefitInput" placeholder="Benefits"></textarea>
                <textarea id="productMainingInput" name="productMainingInput" placeholder="Main Ingredients"></textarea>
                <textarea id="productIngInput" name="productIngInput" placeholder="Ingredients"></textarea>
                <textarea id="productDescInput" name="productDescInput" placeholder="Product Description"></textarea>
            </div>
        </div>
    </form>
    <script src="../adminjs/products.js"> </script>
    <script src="../adminjs/chosenappear.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
