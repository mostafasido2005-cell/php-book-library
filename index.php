<?php
// index.php
// Student: Mostafa Mamdouh Abu Sido - ID: 120231590

$genres = ["Novel","Science","History","Programming","Religion"];

$books = [
    ["id"=>1,"title"=>"Clean Code","author"=>"Robert Martin","genre"=>"Programming","year"=>2008,"pages"=>464],
    ["id"=>2,"title"=>"The Alchemist","author"=>"Paulo Coelho","genre"=>"Novel","year"=>1988,"pages"=>208],
    ["id"=>3,"title"=>"Brief History of Time","author"=>"Stephen Hawking","genre"=>"Science","year"=>1988,"pages"=>256],
];

$errors = [];
$success = "";

// تعديل كتاب
if (isset($_GET["edit_id"])) {
    $editId = (int)$_GET["edit_id"];
    $editBook = null;
    foreach($books as $b){
        if($b["id"]==$editId){ $editBook=$b; break; }
    }
}

// حذف كتاب
if (isset($_POST["delete_id"])) {
    $deleteId = (int)$_POST["delete_id"];
    foreach($books as $k=>$b){
        if($b["id"]==$deleteId){
            unset($books[$k]);
            $success="تم حذف الكتاب بنجاح!";
        }
    }
}

// إضافة/تعديل كتاب
if ($_SERVER["REQUEST_METHOD"]==="POST" && !isset($_POST["delete_id"])) {
    $title = trim($_POST["title"]??"");
    $author = trim($_POST["author"]??"");
    $genre = $_POST["genre"]??"";
    $year = $_POST["year"]??"";
    $pages = $_POST["pages"]??"";

    $currentYear = date("Y");
    if(strlen($title)<3||strlen($title)>120) $errors["title"]="العنوان بين 3 و 120 حرف.";
    if(str_word_count($author)<2) $errors["author"]="اسم المؤلف يجب أن يحتوي على اسمين.";
    if(!preg_match("/^\d{4}$/",$year)||$year<1000||$year>$currentYear) $errors["year"]="السنة غير صحيحة.";
    if(!is_numeric($pages)||$pages<=0) $errors["pages"]="عدد الصفحات يجب أن يكون موجب.";
    if(!in_array($genre,$genres)) $errors["genre"]="النوع غير صحيح.";

    if(empty($errors)){
        if(isset($_POST["edit_id"]) && $_POST["edit_id"]!=""){
            foreach($books as &$b){
                if($b["id"]==(int)$_POST["edit_id"]){
                    $b["title"]=htmlspecialchars($title);
                    $b["author"]=htmlspecialchars($author);
                    $b["genre"]=$genre;
                    $b["year"]=$year;
                    $b["pages"]=$pages;
                    $success="تم تعديل الكتاب بنجاح!";
                }
            }
        } else {
            $newId = count($books)+1;
            $books[]=["id"=>$newId,"title"=>htmlspecialchars($title),
                      "author"=>htmlspecialchars($author),
                      "genre"=>$genre,"year"=>$year,"pages"=>$pages];
            $success="تمت إضافة الكتاب بنجاح!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <title>مكتبة الكتب</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
  <h2 class="mb-4">📚 مكتبة شخصية</h2>
  <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
  <div class="row">
    <!-- النموذج -->
    <div class="col-md-4">
      <form method="post" class="border p-3 bg-light">
        <input type="hidden" name="edit_id" value="<?= $editBook["id"]??"" ?>">
        <div class="mb-3">
          <label class="form-label">العنوان</label>
          <input type="text" name="title" class="form-control" value="<?= $editBook["title"]??"" ?>">
          <?php if(isset($errors["title"])): ?><div class="text-danger"><?= $errors["title"] ?></div><?php endif; ?>
        </div>
        <div class="mb-3">
          <label class="form-label">المؤلف</label>
          <input type="text" name="author" class="form-control" value="<?= $editBook["author"]??"" ?>">
          <?php if(isset($errors["author"])): ?><div class="text-danger"><?= $errors["author"] ?></div><?php endif; ?>
        </div>
        <div class="mb-3">
          <label class="form-label">النوع</label>
          <select name="genre" class="form-select">
            <?php foreach($genres as $g): ?>
              <option value="<?= $g ?>" <?= ($editBook["genre"]??"")==$g?"selected":"" ?>><?= $g ?></option>
            <?php endforeach; ?>
          </select>
          <?php if(isset($errors["genre"])): ?><div class="text-danger"><?= $errors["genre"] ?></div><?php endif; ?>
        </div>
        <div class="mb-3">
          <label class="form-label">السنة</label>
          <input type="number" name="year" class="form-control" value="<?= $editBook["year"]??"" ?>">
          <?php if(isset($errors["year"])): ?><div class="text-danger"><?= $errors["year"] ?></div><?php endif; ?>
        </div>
        <div class="mb-3">
          <label class="form-label">عدد الصفحات</label>
          <input type="number" name="pages" class="form-control" value="<?= $editBook["pages"]??"" ?>">
          <?php if(isset($errors["pages"])): ?><div class="text-danger"><?= $errors["pages"] ?></div><?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary"><?= isset($editBook)?"تعديل الكتاب":"إضافة كتاب" ?></button>
      </form>
    </div>
    <!-- الجدول -->
    <div class="col-md-8">
      <table class="table table-striped">
        <thead class="table-dark">
          <tr>
            <th>ID</th><th>العنوان</th><th>المؤلف</th><th>النوع</th><th>السنة</th><th>الصفحات</th><th>إجراءات</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($books as $b): ?>
            <tr>
              <td><?= $b["id"] ?></td>
              <td><?= $b["title"] ?></td>
              <td><?= $b["author"] ?></td>
              <td><?= $b["genre"] ?></td>
              <td><?= $b["year"] ?></td>
              <td><?= $b["pages"] ?></td>
              <td>
                <a href="?edit_id=<?= $b["id"] ?>" class="btn btn-sm btn-warning">تعديل</a>
                <!-- زر الحذف يفتح Modal -->
                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $b["id"] ?>">حذف</button>
                <!-- Modal -->
                <div class="modal fade" id="deleteModal<?= $b["id"] ?>" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header"><h5 class="modal-title">تأكيد الحذف</h5></div>
                      <div class="modal-body">هل أنت متأكد من حذف الكتاب "<?= $b["title"] ?>"؟</div>
                      <div class="modal-footer">
                        <form method="post">
                          <input type="hidden" name="delete_id" value="<?= $b["id"] ?>">
                          <button type="submit" class="btn btn-danger">نعم، حذف</button>
                        </form>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                      </div>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
