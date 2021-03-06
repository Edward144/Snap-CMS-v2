<?php require_once('includes/header.php'); ?>

<?php checkAccess($_SESSION['adminlevel']); ?>

<div class="flexContainer" id="userManagement">
    <div class="column column-60 currentUsers">
        <h2 class="greyHeader">Current Users</h2>
        
        <div>
            <?php echo ($_SESSION['adminlevel'] > 0 ? '<p>As you are not an admin user, you only have permission to view and update your own details.</p>' : ''); ?>
            
            <div class="hasTable">
                <?php 
                if(isset($_SESSION['adminlevel']) && $_SESSION['adminlevel'] == 0) {
                    $users = $mysqli->query("SELECT * FROM `users`"); 
                }
                else {
                    $users = $mysqli->query("SELECT * FROM `users` WHERE username = '{$_SESSION['adminusername']}'"); 
                }

                if($users->num_rows > 0) : 
                ?>
                    <form class="editUser" method="POST" action="scripts/editUser.php">
                        <table class="formattedTable">
                            <thead>
                                <th>Username</th>
                                <th>Name</th>
                                <th>Access Level</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th>Actions</th>
                            </thead>

                            <tbody>
                                <?php while($row = $users->fetch_assoc()) : ?>
                                    <tr>
                                        <td>
                                            <input type="hidden" name="userId" value="<?php echo $row['id']; ?>">
                                            <input type="hidden" name="oUsername" value="<?php echo $row['username']; ?>">
                                            <?php if($_SESSION['adminusername'] == 'admin' && $row['username'] != 'admin') : ?>
                                                <input type="text" name="username" value="<?php echo $row['username']; ?>">
                                            <?php else : ?>
                                                <input type="hidden" name="username" value="<?php echo $row['username']; ?>">
                                                <span><?php echo $row['username']; ?></span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <label>First Name</label>
                                            <input type="text" name="firstName" value="<?php echo $row['first_name']; ?>">
                                            <br>
                                            <label>Last Name</label>
                                            <input type="text" name="lastName" value="<?php echo $row['last_name']; ?>">
                                        </td>

                                        <td>
                                            <?php if($_SESSION['adminusername'] == 'admin' && $row['username'] != 'admin') : ?>
                                                <input type="number" step="1" min="0" name="accessLevel" value="<?php echo $row['access_level']; ?>">
                                            <?php else : ?>
                                                <input type="hidden" name="username" value="<?php echo $row['username']; ?>">
                                                <span><?php echo $row['access_level']; ?></span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <input type="text" name="email" value="<?php echo $row['email']; ?>">
                                        </td>

                                        <td>
                                            <label>New Password</label>
                                            <input type="password" name="password">
                                            <br>
                                            <label>Confirm Password</label>
                                            <input type="password" name="passwordConf">
                                        </td>

                                        <td>
                                            <input type="button" value="Update" name="update">
                                            <?php if($_SESSION['adminusername'] != $row['username']) : ?>
                                                <input type="button" value="Delete" name="delete" class="redButton">
                                            <?php else : ?>
                                                <input type="button" style="visibility: hidden; width: 51.86px;">
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </form>
                
                    <script>
                        $(".currentUsers .editUser input[name='update']").click(function() {
                            $(".currentUsers #message").text("");
                            
                            var row = $(this).closest("tr");
                            var action = 1;
                            
                            var id = row.find("input[name='userId']").val();
                            var username = row.find("input[name='username']").val();
                            var oUser = row.find("input[name='oUsername']").val();
                            var firstName = row.find("input[name='firstName']").val();
                            var lastName = row.find("input[name='lastName']").val();
                            var email = row.find("input[name='email']").val();
                            var password = row.find("input[name='password']").val();
                            var passwordConf = row.find("input[name='passwordConf']").val();
                            
                            if(username == "") {
                                $(".currentUsers #message").text(oUser + ": Username cannot be empty");
                                return;
                            }
                            
                            if(email == "") {
                                $(".currentUsers #message").text(oUser + ": Email is required.");
                                return;
                            }

                            if(email.indexOf("@") < 0) {
                                $(".currentUsers #message").text(oUser + ": Email is invalid.");
                                return;
                            }
                            else if(email.split("@")[1].indexOf(".") < 0) {
                                $(".currentUsers #message").text(oUser + ": Email is invalid.");
                                return;
                            }

                            if(password.length < 8 && password.length > 0) {
                                $(".currentUsers #message").text(oUser + ": Password must be at least 8 characters.");
                                return;
                            }
                            else if(password != passwordConf) {
                                $(".currentUsers #message").text(oUser + ": Password does not match.");
                                return;
                            }
                            
                            $.ajax({
                                url: "scripts/editUser.php",
                                method: "POST",
                                dataType: "json",
                                data: ({action, id, oUser, username, firstName, lastName, email, password, passwordConf}),
                                success: function(data) {
                                    $(".currentUsers #message").text(data);
                                }
                            });
                        });
                        
                        $(".currentUsers .editUser input[name='delete']").click(function() {
                            $(".currentUsers #message").text("");
                            
                            var row = $(this).closest("tr");
                            var action = 0;
                            
                            var id = row.find("input[name='userId']").val();
                            var oUser = row.find("input[name='oUsername']").val();
                            
                            if(confirm("Are you sure you want to delete " + oUser + "?")) {
                                $.ajax({
                                    url: "scripts/editUser.php",
                                    method: "POST",
                                    dataType: "json",
                                    data: ({action, id, oUser}),
                                    success: function(data) {
                                        if(data[0] == 1) {
                                            row.remove();
                                        }
                                        
                                        $(".currentUsers #message").text(data[1]);
                                    }
                                });
                            }
                        });
                    </script>
                <?php endif; ?>
            </div>
            
            <p id="message" style="margin-top: 1em;"></p>
        </div>
    </div>

    <?php if(isset($_SESSION['adminlevel']) && $_SESSION['adminlevel'] == 0) : ?>
        <div class="column column-40 createUser formBlock">
            <h2 class="greyHeader">Add User</h2>

            <div>            
                <form id="createUser">
                    <p>
                        <label>First Name</label>
                        <input type="text" name="firstName">
                    </p>

                    <p>
                        <label>Last Name</label>
                        <input type="text" name="lastName">
                    </p>

                    <p>
                        <label>Username</label>
                        <input type="text" name="username">
                    </p>

                    <p>
                        <label>Email Address</label>
                        <input type="text" name="email">
                    </p>

                    <p>
                        <label>Password</label>
                        <input type="password" name="password">
                    </p>

                    <p>
                        <label>Confirm Password</label>
                        <input type="password" name="passwordConf">
                    </p>

                    <p>
                        <label>Access Level</label>
                        <input type="number" step="1" min="0" name="accessLevel" value="0">
                    </p>

                    <p style="color: red;">Lower access level numbers can have access to more of the system.</p>

                    <input type="submit" value="Submit">

                    <p id="message"></p>
                </form>

                <script>
                    $("#createUser input[type='submit']").click(function() {
                        event.preventDefault();

                        var action = 2;

                        var username = $("#createUser input[name='username']").val();
                        var firstName = $("#createUser input[name='firstName']").val();
                        var lastName = $("#createUser input[name='lastName']").val();
                        var email = $("#createUser input[name='email']").val();
                        var password = $("#createUser input[name='password']").val();
                        var passwordConf = $("#createUser input[name='passwordConf']").val();

                        if(username == "") {
                            $("#createUser #message").text("Username cannot be empty");
                            return;
                        }

                        if(email == "") {
                            $("#createUser #message").text("Email is required.");
                            return;
                        }

                        if(email.indexOf("@") < 0) {
                            $("#createUser #message").text("Email is invalid.");
                            return;
                        }
                        else if(email.split("@")[1].indexOf(".") < 0) {
                            $("#createUser #message").text("Email is invalid.");
                            return;
                        }

                        if(password.length < 8 && password.length > 0) {
                            $("#createUser #message").text("Password must be at least 8 characters.");
                            return;
                        }
                        else if(password != passwordConf) {
                            $("#createUser #message").text("Password does not match.");
                            return;
                        }

                        $.ajax({
                            url: "scripts/editUser.php",
                            method: "POST",
                            dataType: "json",
                            data: ({action, username, firstName, lastName, email, password, passwordConf}),
                            success: function(data) {
                                if(data[0] == 1) {
                                    $("#createUser input:not([type='submit'])").val("");
                                }

                                console.log(data);

                                $("#createUser #message").text(data[1]);
                            }
                        });
                    })
                </script>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once('includes/footer.php'); ?>