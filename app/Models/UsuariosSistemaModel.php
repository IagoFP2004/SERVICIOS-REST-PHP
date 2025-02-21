<?php
declare(strict_types=1);
namespace Com\Daw2\Models;

use Com\Daw2\Core\BaseDbModel;
use PDO;
use phpseclib3\File\ASN1\Maps\NameConstraints;

class UsuariosSistemaModel extends BaseDbModel
{

    public function login(string $email): array|false
    {
        $sql="SELECT * FROM usuario_sistema WHERE email=:email";
        $stmt=$this->pdo->prepare($sql);
        $stmt->execute(['email'=>$email]);
        return $stmt->fetch();
    }
}