<?php

    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    require_once "src/Category.php";
    require_once "src/Task.php";

    $server = 'mysql:host=localhost:8889;dbname=to_do_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class CategoryTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
            {
              Category::deleteAll();
              Task::deleteAll();
            }

            function testGetName()
            {
                //Arrange
                $name = "Kitchen chores";
                $test_category = new Category($name);

                //Act
                $result = $test_category->getName();

                //Assert
                $this->assertEquals($name, $result);

            }

            function testSetName()
            {
                //Arrange
                $name = "Kitchen chores";
                $test_category = new Category($name);

                //Act
                $test_category->setName("Home chores");
                $result = $test_category->getName();

                //Assert
                $this->assertEquals("Home chores", $result);
            }

            function testGetId()
            {
                //Arrange
                $name = "Work stuff";
                $id = 1;
                $test_category = new Category($name, $id);

                //Act
                $result = $test_category->getId();

                //Assert
                $this->assertEquals(1, $result);
            }

            function testSave()
            {
                //Arrange
                $name = "Work stuff";
                $id = 1;
                $test_category = new Category($name, $id);
                $test_category->save();

                //Act
                $result = Category::getAll();

                //Assert
                $this->assertEquals($test_category, $result[0]);
            }

            function testUpdate()
            {
                //Arrange
                $name = "Work stuff";
                $id = 1;
                $test_category = new Category($name, $id);
                $test_category->save();

                $new_name = "Home stuff";

                //Act
                $test_category->update($new_name);

                //Assert
                $this->assertEquals("Home stuff", $test_category->getName());
            }

            function testDelete()
            {
                //Arrange
                $name = "Work stuff";
                $id = 1;
                $test_category = new Category($name, $id);
                $test_category->save();

                $description = "File reports";
                $test_task = new Task($description, $id);
                $test_task->save();

                //Act
                $test_category->delete();

                //Assert
                $this->assertEquals([], $test_category->getTasks());
            }

            function testGetAll()
            {
                //Arrange
                $name = "Work stuff";
                $id = 1;
                $name2 = "Home stuff";
                $id2 = 2;
                $test_category = new Category($name, $id);
                $test_category->save();
                $test_category2 = new Category($name2, $id2);
                $test_category2->save();

                //Act
                $result = Category::getAll();

                //Assert
                $this->assertEquals([$test_category, $test_category2], $result);
            }

            function testDeleteAll()
            {
                //Arrange
                $name = "Wash the dog";
                $id = 1;
                $test_category = new Category($name, $id);
                $test_category->save();

                $name2 = "Water the lawn";
                $id2 = 2;
                $test_category2 = new Category($name2, $id2);
                $test_category2->save();

                //Act
                Category::deleteAll();

                //Assert
                $result = Category::getAll();
                $this->assertEquals([], $result);
            }

            function testFind()
            {
                //Arrange
                $name = "Wash the dog";
                $id = 1;
                $test_category = new Category($name, $id);
                $test_category->save();

                $name2 = "Home stuff";
                $id2 = 2;
                $test_category2 = new Category($name2, $id2);
                $test_category2->save();

                //Act
                $result = Category::find($test_category->getId());

                //Assert
                $this->assertEquals($test_category, $result);
            }

            function testAddTask()
            {
                //Arrange
                $name = "Work stuff";
                $id = 1;
                $test_category = new Category($name, $id);
                $test_category->save();

                $description = "File report";
                $id2 = 2;
                $test_task = new Task($description, $id2);
                $test_task->save();

                $description2 = "Take out the trash";
                $id3= 3;
                $test_task2 = new Task($description2, $id3);
                $test_task2->save();

                //Act
                $test_category->addTask($test_task);
                $test_category->addTask($test_task2);

                //Assert
                $this->assertEquals($test_category->getTasks(), [$test_task, $test_task2]);
            }
    }
?>
