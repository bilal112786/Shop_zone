<?php
include 'server/connection.php';

// 1. determine page no
if (isset($_GET['page_no']) && $_GET['page_no'] != "") {
    $page_no = $_GET['page_no'];
} else {
    $page_no = 1;
}

// default values
$category = "Technology"; // default category

// If search form submitted
if (isset($_POST['search'])) {
    if (!empty($_POST['category'])) {
        $category = $_POST['category'];
    }
    if (!empty($_POST['keyword'])) {
        $keyword = $_POST['keyword'];
    }
} else {
    $keyword = "";
}

// 2. get total records
$stmt1 = $conn->prepare("SELECT COUNT(*) AS total_records 
                         FROM blogs 
                         WHERE blog_category = ? 
                         AND (blog_title LIKE ? OR blog_content LIKE ?)");
$search_keyword = "%$keyword%";
$stmt1->bind_param("sss", $category, $search_keyword, $search_keyword);
$stmt1->execute();
$stmt1->bind_result($total_records);
$stmt1->store_result();
$stmt1->fetch();

// 3. pagination setup
$total_records_per_page = 5;
$offset = ($page_no - 1) * $total_records_per_page;

$previous_page = $page_no - 1;
$next_page = $page_no + 1;
$total_no_of_pages = ceil($total_records / $total_records_per_page);

// 4. fetch blogs
$stmt2 = $conn->prepare("SELECT * FROM blogs 
                         WHERE blog_category = ? 
                         AND (blog_title LIKE ? OR blog_content LIKE ?)
                         ORDER BY blog_date DESC
                         LIMIT ?, ?");
$stmt2->bind_param("sssii", $category, $search_keyword, $search_keyword, $offset, $total_records_per_page);
$stmt2->execute();
$blogs = $stmt2->get_result();
?>

<?php include('layouts/header.php'); ?>

<div class="container my-5">
    <div class="row">
        <!-- Sidebar Search -->
        <div class="col-lg-3 col-md-4 col-sm-12">
            <form action="blogs.php" method="POST">
                <p>Category</p>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category" value="Technology" id="cat_tech" <?php if($category=="Technology") echo "checked"; ?>>
                    <label class="form-check-label" for="cat_tech">Technology</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category" value="Lifestyle" id="cat_life" <?php if($category=="Lifestyle") echo "checked"; ?>>
                    <label class="form-check-label" for="cat_life">Lifestyle</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category" value="Fashion" id="cat_fashion" <?php if($category=="Fashion") echo "checked"; ?>>
                    <label class="form-check-label" for="cat_fashion">Fashion</label>
                </div>

                <div class="mt-4">
                    <p>Keyword</p>
                    <input type="text" name="keyword" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($keyword); ?>">
                </div>

                <div class="form-group mt-3">
                    <input type="submit" name="search" class="btn btn-primary w-100" value="Search">
                </div>
            </form>
        </div>

        <!-- Blogs Section -->
        <div class="col-lg-9 col-md-8 col-sm-12">
            <h2>Our Latest Blogs</h2>
            <div class="row">
                <?php while ($blog = $blogs->fetch_assoc()) { ?>
                    <div class="col-lg-6 col-md-12 mb-4">
                        <div class="card">
                            <img class="card-img-top" src="./assets/images/blogs/<?php echo $blog['blog_image']; ?>" alt="blog image" style="height:200px; object-fit:cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $blog['blog_title']; ?></h5>
                                <p class="card-text"><?php echo substr($blog['blog_content'], 0, 120); ?>...</p>
                                <a href="single_blog.php?blog_id=<?php echo $blog['blog_id']; ?>" class="btn btn-primary">Read More</a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mt-4">
                    <li class="page-item <?php if($page_no <= 1){ echo 'disabled'; } ?>">
                        <a class="page-link" href="<?php if($page_no <= 1){ echo '#'; } else { echo "?page_no=".$previous_page; } ?>">Previous</a>
                    </li>

                    <?php for ($i=1; $i<=$total_no_of_pages; $i++) { ?>
                        <li class="page-item <?php if($i==$page_no) echo 'active'; ?>">
                            <a class="page-link" href="?page_no=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php } ?>

                    <li class="page-item <?php if($page_no >= $total_no_of_pages){ echo 'disabled'; } ?>">
                        <a class="page-link" href="<?php if($page_no >= $total_no_of_pages){ echo '#'; } else { echo "?page_no=".$next_page; } ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<?php include('layouts/footer.php'); ?>
