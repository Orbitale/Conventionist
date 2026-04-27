#!/usr/bin/env bash

set -eux

# Recrée une BDD
symfony console doctrine:database:drop --force --no-interaction
symfony console doctrine:database:create --no-interaction

# Place la BDD dans l'état "avant l'ajout de $room dans TimeSlot"
symfony console doctrine:migration:migrate --no-interaction DoctrineMigrations\\Version20250319080855

# Désactive l'ajout de "$room"
sed -i "/targetEntity: Room/s/^[^\/]/\/\//" src/Entity/TimeSlot.php
sed -i "/targetEntity: Room/{n;s/^[^\/]/\/\//}" src/Entity/TimeSlot.php
sed -i "/private Room \$room/s/^[^\/]/\/\//" src/Entity/TimeSlot.php

# Désactive le champ "room" dans les fixtures
sed -i "s/^[^\/] *'room.*$/\/\/\0/" src/DataFixtures/TimeSlotFixture.php

# Insère les fixtures
symfony console doctrine:fixtures:load --no-interaction

# Exécute les autres migrations
symfony console doctrine:migration:migrate --no-interaction
