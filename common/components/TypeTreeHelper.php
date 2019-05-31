<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Common\components
 * @author   Дмитрий Логачев <demonwork@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

namespace common\components;

use yii\db\ActiveRecord;

/**
 * This is the class TypeTreeHelper.
 *
 * @category Category
 * @package  Common\components
 * @author   Дмитрий Логачев <demonwork@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class TypeTreeHelper
{
    /**
     * Построение вспомогательной таблицы для построения дерева типов.
     *
     * @param array $closureTable Список элементов
     * @param array $indexTable Вспомогательная таблица
     *
     * @return void
     */
    public static function indexClosure(&$closureTable, &$indexTable)
    {
        foreach ($closureTable as $item) {
            $indexTable['parents'][$item->parent][] = [
                'id' => $item->child,
                'text' => $item->child0->title];
            $indexTable['children'][$item->child][] = [
                'id' => $item->parent,
                'text' => $item->parent0->title
            ];
            $indexTable['levels']['forward'][$item->child]
                = isset($indexTable['levels']['forward'][$item->child])
                ? $indexTable['levels']['forward'][$item->child] + 1
                : 1;
        }

        if (count($indexTable) == 0) {
            return;
        }

        foreach ($indexTable['levels']['forward'] as $key => $level) {
            $indexTable['levels']['backward'][$level][] = $key;
        }
    }

    /**
     * Построение дерева типов.
     *
     * @param integer $id Id элемента
     * @param array $indexTable Таблица индексов
     *
     * @return array
     */
    public static function closureToTree($id, &$indexTable)
    {
        if (!isset($indexTable['parents'][$id])) {
            return [];
        }

        $level = $indexTable['levels']['forward'][$id];
        $tmp_array = array();
        $link = array();
        foreach ($indexTable['parents'][$id] as $value) {
            $tmp_array[] = $value['id'];
            $link[$value['id']] = [
                'text' => $value['text'],
                'id' => $value['id'],
            ];
        }

        if (isset($indexTable['levels']['backward'][$level + 1])) {
            $tmpArray = $indexTable['levels']['backward'][$level + 1];
        } else {
            $tmpArray = array();
        }

        $tree = array_intersect(
            $tmp_array,
            $tmpArray
        );

        if (count($tree)) {
            $result = array_combine($tree, array_fill(1, count($tree), []));
        } else {
            $result = array();
        }

        foreach ($result as $idx => $item) {
            $result[$idx] = [
                'text' => $link[$idx]['text'],
                'id' => $link[$idx]['id'],
                'nodes' => []
            ];
        }

        foreach ($result as $child => $subtree) {
            if (count($indexTable['parents'][$child]) > 1) {
                $result[$child]['nodes'] = self::closureToTree($child, $indexTable);
            }
        }

        return $result;
    }

    /**
     * Изменяет рекурсивно индексы элементов массива на 0,1,2,3...
     * На каждый уровень "добавляет" элементы ему соответствующие.
     *
     * @param array $tree Массив в котором нужно изменить индексы
     * @param ActiveRecord|string $typeClass Класс типа сущности
     * @param ActiveRecord|string $entityClass Класс сущности
     * @param string $linkField Поле через которое связывается
     * сущность с типом
     *
     * @return array
     */
    public static function resetMulti($tree, $typeClass, $entityClass, $linkField)
    {
        if (is_array($tree)) {
            $tree = array_slice($tree, 0);

            foreach ($tree AS $key => $value) {

                if (is_array($value)) {
                    $tree[$key] = self::resetMulti(
                        $value, $typeClass, $entityClass, $linkField
                    );
                }
            }
        }

        if (isset($tree['id'])) {
            $type = $typeClass::findOne($tree['id']);
            $items = $entityClass::find()
                ->where([$linkField => $type->uuid])
                ->orderBy('title')
                ->all();
            foreach ($items as $item) {
                $a = '<a href="/' . self::_getClassUrl($entityClass) . '/view?id='
                    . $item->_id . '">' . $item->title . '</a>';
                $tree['nodes'][] = ['text' => $a];
            }
        }

        return ($tree);
    }

    /**
     * Конвертирует имя класса в строку разбитую по заглавным буквам
     * и дополняет дефисом
     *
     * @param string $name Имя класса
     *
     * @return string
     */
    private static function _getClassUrl($name)
    {
        $parts = preg_split('/\\\/', $name);

        $resultArray = $parts[count($parts) - 1];

        $pieces = preg_split('/(?=[A-Z])/', $resultArray);

        return strtolower(ltrim(implode('-', $pieces), '-'));
    }

    /**
     * Получаем id родителя для заданного потомка
     *
     * @param integer $id Ид потомка
     * @param ActiveRecord|string $typeClass Класс типа
     * @param ActiveRecord|string $treeClass Класс дерева типа
     *
     * @return integer
     */
    public static function getParentId($id, $typeClass, $treeClass)
    {
        $indexTable = array();
        $parentId = -1;

        $typesTree = $treeClass::find()
            ->from([$treeClass::tableName() . ' as ttt'])
            ->innerJoin(
                $typeClass::tableName() . ' as tt',
                '`tt`.`_id` = `ttt`.`child`'
            )
            ->all();

        self::indexClosure($typesTree, $indexTable);
        if (empty($indexTable)) {
            return $parentId;
        }

        $elementLevel = $indexTable['levels']['forward'][$id];
        $parentLevel = $elementLevel - 1;

        if ($parentLevel !== 0) {
            $elementsOnParentLevel = $indexTable['levels']['backward'][$parentLevel];
            if (count($elementsOnParentLevel) === 1) {
                // единственный элемент на уровне
                $parentId = $elementsOnParentLevel[0];
            } else {
                // запускаем цикл для поиска родителя элемента
                foreach ($elementsOnParentLevel as $seemParentId) {
                    foreach ($indexTable['parents'][$seemParentId] as $p) {
                        if ($p['id'] == $id) {
                            $parentId = $seemParentId;
                            break;
                        }
                    }
                }
            }
        } else {
            // это значит что элемент расположен в корне
            // parentUuid = '00000000-0000-0000-0000-000000000000'
        }

        return $parentId;
    }

    /**
     * Метод для перевешивания элемента под нового родителя со всей его иерархией.
     *
     * @param integer $id Ид элемента
     * @param string $parentUuid uuid нового родителя для элемента
     * @param ActiveRecord|string $typeClass Класс типа
     * @param ActiveRecord|string $treeClass Класс дерева типа
     *
     * @return void
     */
    public static function moveTree($id, $parentUuid, $typeClass, $treeClass)
    {
        // родитель
        $parent = $typeClass::find()->where(['uuid' => $parentUuid])->one();
        if ($parent) {
            $newParentId = $parent->_id;
        } else {
            $newParentId = -1;
        }

        // проверяем нужно ли "перевешивать" элемент под другого родителя
        $currentParentId = self::getParentId($id, $typeClass, $treeClass);
        if ($currentParentId == $newParentId) {
            // родитель не изменился, ни чего не делаем
            return;
        }

        // строим вспомогательную таблицу
        $indexTable = array();
        $typesTree = $treeClass::find()
            ->from([$treeClass::tableName() . ' as ttt'])
            ->innerJoin(
                $typeClass::tableName() . ' as tt',
                '`tt`.`_id` = `ttt`.`child`'
            )
            ->all();
        TypeTreeHelper::indexClosure($typesTree, $indexTable);

        if ($newParentId == -1) {
            // если родитель был выбран "Корень", привязываем элемент к самому себе
            // проверку на перенос под потомка делать не нужно, премещаем в корень
            $newParentId = $id;
        } else {
            // проверяем на то чтоб текущий родитель не стал потомком своего потомка
            foreach ($indexTable['parents'][$id] as $children) {
                if ($children['id'] == $newParentId) {
                    // попытка переместить родителя под потомка
                    // ни чего не делаем
                    return;
                }
            }
        }

        // ищем всех потомков данного типа
        $tree = self::closureToTree($id, $indexTable);

        // удаляем иерархию для элемента и его потомков
        $treeTable = $treeClass::tableName();
        $sql = 'DELETE FROM ' . $treeTable . ' WHERE child IN ('
            . 'SELECT child FROM '
            . '(SELECT child FROM ' . $treeTable . ' WHERE parent = '
            . $id
            . ') AS tmptable)';
        \Yii::$app->db->createCommand($sql)->execute();

        // подвешиваем элемент к новому родителю
        self::addTree($id, $newParentId, $treeClass);

        // подвешиваем потомков к элементу который перенесли
        self::_addNewTree($id, $tree, $treeClass);
    }

    /**
     * Метод рекурсивно добавляет потомков для указанного родителя.
     *
     * @param integer $parent Родитель
     * @param array $node Массив потомков
     * @param ActiveRecord $treeClass Класс дерева сущности
     *
     * @return void
     */
    private static function _addNewTree($parent, $node, $treeClass)
    {
        foreach ($node as $id => $item) {
            self::addTree($id, $parent, $treeClass);
            if (count($item['nodes'])) {
                self::_addNewTree($id, $item['nodes'], $treeClass);
            }
        }
    }

    /**
     * Создаёт иерархию для element который является потомком child
     *
     * @param integer $element Элемент
     * @param integer $parent Родитель элемента
     * @param ActiveRecord|string $treeClass Класс дерева сущности
     *
     * @return void
     */
    public static function addTree($element, $parent, $treeClass)
    {
        // добавляем в базу иерархию связей типов
        $table = $treeClass::tableName();
        $sql = 'INSERT INTO ' . $table . ' (parent, child) SELECT parent, '
            . $element
            . ' FROM ' . $table . ' WHERE child = ' . $parent
            . ' UNION ALL SELECT ' . $element . ', ' . $element;
        \Yii::$app->db->createCommand($sql)->execute();
    }
}
