<?php

namespace Interart\Flywork\Library;

/**
 * Security library.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 * @version     1.0
 */
final class Security
{
    private $password_algorithm;
    private $security_config = [
        'cipher' => 'AES-256-XTS',
        'key'    => '2b66f87fdac6830e',
        'iv'     => '58077f401138b82f',
    ];

    public function __construct(string $cipherAlgorithm = 'AES-256-XTS', string $cipherKey = '', string $cipherIv = '', int $passwordAlgorithm = PASSWORD_DEFAULT)
    {
        if (!empty($cipherAlgorithm)) {
            $this->security_config['cipher'] = $cipherAlgorithm;
        }

        if (!empty($cipherKey)) {
            $this->security_config['key'] = $cipherKey;
        }

        if (!empty($cipherIv)) {
            $this->security_config['iv'] = $cipherIv;
        }

        $this->password_algorithm = $passwordAlgorithm;
    }

    public function comparePassword(string $plainPassword, string $encryptedPassword)
    {
        return password_verify($plainPassword, $encryptedPassword);
    }

    public function encryptPassword(string $plainPassword)
    {
        return password_hash($plainPassword, PASSWORD_DEFAULT);
    }

    public function encrypt($plain)
    {
        return base64_encode(openssl_encrypt(json_encode($plain), $this->security_config['cipher'], $this->security_config['key'], 0, $this->security_config['iv']));
    }

    public function decrypt($encrypted)
    {
        if (empty($encrypted)) {
            return null;
        }
        return json_decode(trim(openssl_decrypt(base64_decode($encrypted), $this->security_config['cipher'], $this->security_config['key'], 0, $this->security_config['iv'])));
    }

}
