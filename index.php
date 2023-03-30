
<style>


body {
    width: 100%;
    height: 100%;
    background-color: black;
    color: white;
}
textarea {
    width: 100%;
    height: 100%;
}

h1 {
  text-align: center;
  margin-top: 50px;
}


.success {
  color: green;
}


form {
  margin-top: 30px;
  display: flex;
  flex-direction: column;
  align-items: center;
}


label {
  margin-bottom: 10px;
}



input[type="submit"] {
  padding: 10px 20px;
  border-radius: 5px;
  border: none;
  background-color: #4CAF50;
  color: white;
  font-weight: bold;
  cursor: pointer;
}

input[type="text"] {
  width: 100%;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
  margin-top: 6px;
  margin-bottom: 16px;
  font-size: 16px;
}

input[type="text"]:focus {
  border: 2px solid #555;
}

</style>

<?php
session_start();

$ip_message = '';
$htaccess_content = '';

if (isset($_POST['submit'])) {
    $htaccess_content = isset($_POST['htaccess_content']) ? $_POST['htaccess_content'] : '';
    file_put_contents('.htaccess', $htaccess_content);
    $success_message = 'Fișierul .htaccess a fost actualizat cu succes.';

    // Adaugă adresa IP la fișierul .htaccess
    if(isset($_POST['ip_address'])){
        $file = '.htaccess'; // numele fișierului
        $ip_addresses = explode(' ', $_POST['ip_address']);
        $new_lines = [];

        foreach($ip_addresses as $ip_address){
            $found = false;
            $lines = file($file, FILE_IGNORE_NEW_LINES);

            foreach($lines as $line){
                if(strpos($line, "deny from $ip_address") !== false){
                    $found = true;
                    break;
                } elseif(strpos($line, "deny from ") !== false) {
                    $ips = explode(' ', $line);
                    $unique_ips = array_unique($ips);
                    if(in_array($ip_address, $unique_ips)){
                        $found = true;
                        $new_lines[] = $line; // păstrează liniile care au același IP
                        break;
                    }
                }
            }

            if(!$found){
                // adaugă "deny from" în fața adresei IP și scrie linia în fișier
                $new_line = "deny from $ip_address";
                $new_lines[] = $new_line;
                $ip_message .= '<p>Adresa IP ' . $ip_address . ' a fost adaugata cu succes in fisier.</p>';
            } else {
                $ip_message .= '<p>Adresa IP ' . $ip_address . ' exista deja in fisier si nu a fost adaugata.</p>';
            }
        }

        // concatenează liniile noi cu cele existente, păstrând doar cele cu adrese IP diferite
        $htaccess_content = implode("\n", array_unique($new_lines)) . "\n";

        file_put_contents($file, $htaccess_content);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
<title>CHECK-APP-IP</title>
</head>
<body>


<!DOCTYPE html>
<html>
  <head>
  </head>
  <body>
    <h1>Manage IP Addresses</h1>

    <?php if (isset($success_message)) { ?>
      <p><?php echo $success_message; ?></p>
    <?php } ?>


    
<form method="post" action="">

    <label for="ip_address">Adaugă Adresă IP:</label>
    <input type="text" name="ip_address" id="ip_address" pattern="[0-9\. ]+" required>
    <input type="submit" name="submit" value="Save"> 
    <table style="width: 100%; border-collapse: collapse;">
  <tr>

    <td style="width: 50%; vertical-align: top; padding-left: 10px;">
      <table style="width: 100%; border: 2px solid black; border-collapse: collapse;">
        <tr style="background-color: #ccc; font-weight: bold;">
          <td style="padding: 5px;">#</td>
          <td style="padding: 5px;">Content</td>
        </tr>
        <?php
        $lines = file('.htaccess', FILE_IGNORE_NEW_LINES);
        foreach ($lines as $key => $line) {
          ?>
          <tr>
            <td style="padding: 5px;"><?php echo $key + 1; ?></td>
            <td style="padding: 5px;"><?php echo $line; ?></td>
          </tr>
          <?php
        }
        ?>
      </table>
    </td>
  </tr>
</table>
    
    
</form>
<?php if (isset($ip_message)) { echo $ip_message; } ?>
</body>
</html>