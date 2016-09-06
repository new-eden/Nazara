<?php
/**
 * Created by PhpStorm.
 * User: micha
 * Date: 06-09-2016
 * Time: 21:11
 */

namespace Nazara\Model;


use Nazara\Helper\Mongo;

class Users extends Mongo {
    public $collectionName = "users";
    public $databaseName = "nazara";

    public function storeUser(string $username, $nickname, int $id, int $discriminator, string $avatar, string $status, $game) {
        // Fetch user if exists and add game to gamesPlayed, and update
        $existing = $this->collection->findOne(array("id" => $id));
        if(!empty($existing)) {
            $games = $existing["gamesPlayed"];
            if($game != null && !empty($games)) {
                foreach ($games as $g) {
                    if ($g != $game) {
                        $games[] = $game;
                    }
                }
            }
        }
        elseif($game != null) {
            $games = array($game);
        }
        else {
            $games = array();
        }

        $data = array(
            "username" => $username,
            "nickname" => $nickname,
            "id" => $id,
            "discriminator" => $discriminator,
            "avatar" => $avatar,
            "status" => $status,
            "gamesPlayed" => $games,
            "lastSeen" => $this->makeTimeFromUnixTime(time())
        );

        $this->collection->replaceOne(array("id" => $id), $data, array("upsert" => true));
    }

    public function userCount() {
        return $this->collection->count();
    }
}