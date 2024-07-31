
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <title>Todo List</title>
</head>

<body>
  <div class="container my-5">
    <h2>Todo List</h2>
    <table class="table">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Todo</th>
          <th scope="col">Delete</th>
          <th scope="col">Update</th>
        </tr>
      </thead>
      <tbody>
        <?php

        $todo = new Task();
        $todoList = $todo->getAll();
        if (count($todoList)) {
          foreach ($todoList as $todos) {
            $text_decoration = $todos['status'] ? 'text-decoration-line-through' : '';
            $check_box = $todos['status'] ? 'checked' : '';
            echo "<tr>
            <th>{$todos['id']}</th>
            <td><p style='display: inline' class='{$text_decoration}'>{$todos['text']}</p></td>
            <td><a class='btn btn-danger' href='?delete={$todos['id']}'>Delete</a></td>
            <td><a class='btn btn-success' href='?update={$todos['id']}'>Update</a></td>
            </tr>";
          }
        } else {
          echo '<tr>
          <td colspan="3"><h5 class="text-center">Todo is empty</h5></td>
          </tr>';
        }
        ?>
      </tbody>
    </table>

    <form action="" method="post">
      <div class="mb-3">
        <input type="text" name="text" class="form-control" placeholder="Todo text" required>
        <button type="submit" class="btn btn-primary mt-2">Submit</button>
      </div>
    </form>
  </div>

</body>

</html>