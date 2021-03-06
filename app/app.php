<?php
    date_default_timezone_set('America/Los_Angeles');
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Task.php";
    require_once __DIR__."/../src/Category.php";

    use Symfony\Component\HttpFoundation\Request;
    Request::enableHttpMethodParameterOverride();

    $server = 'mysql:host=localhost:8889;dbname=to_do';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    $app = new Silex\Application();

    $app->register(
        new Silex\Provider\TwigServiceProvider(),
        array('twig.path' => __DIR__.'/../views')
    );

    $app['debug'] = true;

    //INDEX PAGE
    $app->get("/", function() use ($app) {
        return $app['twig']->render('index.html.twig', array('categories' => Category::getAll(), 'tasks' => Task::getAll()));
    });




//TASKS
    //GET
    $app->get("/tasks", function() use ($app) {
        $today = date("Y-m-d");
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll(), 'all_categories' => Category::getAll(), 'overdues'=>Task::getOverDue($today)));
    });
    //POST
    $app->post("/tasks", function() use ($app) {
        $today = date("Y-m-d");
        $description = filter_var($_POST['description'], FILTER_SANITIZE_MAGIC_QUOTES);
        $due_date = $_POST['due_date'];
        $task = new Task($description, 0, null, $due_date);
        $task->save();
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll(), 'overdues'=>Task::getOverDue($today)));
    });

    //Specific Task GET
    $app->get("/task/{id}", function($id) use ($app) {
        $today = date("Y-m-d");
        $task = Task::find($id);
        return $app['twig']->render('task.html.twig', array('task' => $task, 'categories' => $task->getCategories(), 'all_categories' => Category::getAll(), 'overdues'=>Task::getOverDue($today)));
    });

    //Specific POST
    $app->post("/add_categories", function() use ($app) {
        $category = Category::find($_POST['category_id']);
        $task = Task::find($_POST['task_id']);
        $task->addCategory($category);
        return $app['twig']->render('tasks.html.twig', array('task' => $task, 'tasks' => Task::getAll(), 'categories' => $task->getCategories(), 'all_categories' => Category::getAll()));
    });

    //Complete get
    $app->get("/complete/{id}", function($id) use ($app){
        $task = Task::find($id);
        $task->setComplete();
        $task->statusUpdate();
        var_dump(Task::getAll());
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll()));
    });

    $app->get("/completed", function() use ($app) {
        $completed_tasks = Task::getAllCompleted();
        return $app['twig']->render('complete_tasks.html.twig', array("completed_tasks" => $completed_tasks));
    });



//CATEGORIES
    //GET
    $app->get("/categories", function() use ($app) {
        return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll(), 'all_tasks' => Task::getAll()));
    });
    //POST
    $app->post("/categories", function() use ($app) {
        $category = new Category(filter_var($_POST['name'], FILTER_SANITIZE_MAGIC_QUOTES));

        $category->save();
        return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll()));
    });


//CATEGORIES-SPECIFIC
    $app->get("/category/{id}", function($id) use ($app) {
        $category = Category::find($id);
        return $app['twig']->render('category.html.twig', array('category' => $category, 'tasks' => $category->getTasks(), 'all_tasks' => Task::getAll()));
    });

    $app->post("/add_tasks", function() use ($app) {
        $category = Category::find($_POST['category_id']);
        $task = Task::find($_POST['task_id']);
        $category->addTask($task);
        return $app['twig']->render('category.html.twig', array('category' => $category, 'categories' => Category::getAll(), 'tasks' => $category->getTasks(), 'all_tasks' => Task::getAll()));
    });

//Category Edit Page
    $app->get("/category/{id}/edit", function($id) use ($app) {
        $category = Category::find($id);
        return $app['twig']->render('category_edit.html.twig', array('category' => $category));
    });

    $app->patch("/category/{id}/edit", function($id) use ($app) {
        $name = $_POST['name'];
        $category = Category::find($id);
        if ($name) {
            $category->update($name);
        }
        return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll()));
    });



    //EDIT TASKS
    $app->get("/task/{id}/edit", function($id) use ($app) {
        $task = Task::find($id);
        return $app['twig']->render('task_edit.html.twig', array('task' => $task));
    });

    $app->patch("/task/{id}/edit", function($id) use ($app) {
        $description = $_POST['name'];
        $task = Task::find($id);
        if ($description) {
            $task->update($description);
        }
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll()));
    });


    //DELETE
    $app->post("/delete_tasks", function() use ($app) {
        Task::deleteAll();
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll()));
    });

    $app->post("/delete_categories", function() use ($app) {
        Category::deleteAll();
        return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll()));
    });

    $app->delete("/category/{id}", function($id) use ($app) {
        $category = Category::find($id);
        $category->delete();
        return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll()));
    });

    $app->delete("/task/{id}", function($id) use ($app) {
        $task = Task::find($id);
        $task->delete();
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll()));
    });

    return $app;
?>
