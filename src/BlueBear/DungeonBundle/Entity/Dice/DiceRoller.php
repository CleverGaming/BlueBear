<?php

namespace BlueBear\DungeonBundle\Entity\Dice;


class DiceRoller
{
    /**
     * @param $diceCode
     * @return Dice|Dice[]
     */
    public function roll($diceCode)
    {
        $diceArray = explode('d', $diceCode);

        if (!$diceArray[0]) {
            // if no dice number is provided, we assume its one (d12 <=> 1d12)
            $diceArray[0] = 1;
        }
        $numberOfDice = $diceArray[0];
        $size = $diceArray[1];
        $dices = [];

        while ($numberOfDice) {
            $dice = new Dice();
            $dice->size = (int)$size;
            $dice->value = rand(1, $size);

            $dices[] = $dice;
            $numberOfDice--;
        }
        if (count($dices) == 1) {
            $dices = $dices[0];
        }
        return $dices;
    }

    /**
     * @param Dice[] $dices
     */
    public function removeLowest(array &$dices)
    {
        $lowestValue = 1;
        $lowestValueIndex = 0;

        foreach ($dices as $index => $dice) {
            if ($dice->value <= $lowestValue) {
                $lowestValueIndex = $index;
                $lowestValue = $dice->value;
            }
        }
        unset ($dices[$lowestValueIndex]);
    }

    /**
     * @param Dice[] $dices
     * @return int
     */
    public function sum(array $dices)
    {
        $sum = 0;

        foreach ($dices as $dice) {
            $sum += (int)$dice->value;
        }
        return $sum;
    }
}
