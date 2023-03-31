<?php

namespace App\Helper;

use DateTime;
use Doctrine\DBAL\Connection;
use RuntimeException;

class MigrationHelper
{
    public static function createCategory(Connection $connection, string $name): int
    {
        $connection->executeQuery(
            'INSERT INTO category (name) VALUES (?)',
            [$name]
        );

        return $connection->lastInsertId();
    }

    public static function createContent(
        Connection $connection,
        string     $pageType,
        string     $path,
        string     $name,
        int        $parent,
        ?int       $measureId = null,
        ?int       $categoryIdId = null,
        int        $topMenu = 0,
        int        $indexMenu = 0,
        ?string    $menuName = null,
        ?int       $menuOrder = null,
        ?string    $seoTitle = null,
        ?string    $seoDescription = null,
        ?string    $seoText = null,
        ?string    $seoTextHidden = null,
        ?string    $seoTextImage = null,
        ?string    $cardTitle = null,
        ?string    $cardDescription = null,
        ?string    $cardImage = null,
        ?int       $cardPrice = null,
        ?string    $thumbImage = null,
        ?string    $template = null,
        ?string    $updated = null
    ): int
    {
        if (null === $menuName && ($topMenu > 0 || $indexMenu > 0)) {
            $menuName = $name;
        }

        if (null === $updated) {
            $updated = (new DateTime())->format('Y-m-d H:i:s');
        }

        $connection->executeQuery(
            'INSERT INTO content (
                page_type,
                path,
                name,
                parent,
                measure_id,
                category_id_id,
                top_menu,
                index_menu,
                menu_name,
                menu_order,
                seo_title,
                seo_description,
                seo_text,
                seo_text_hidden,
                seo_text_img,
                card_title,
                card_description,
                card_image,
                card_price,
                thumb_img,
                template,
                updated
                ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
            [
                $pageType,
                $path,
                $name,
                $parent,
                $measureId,
                $categoryIdId,
                $topMenu,
                $indexMenu,
                $menuName,
                $menuOrder,
                $seoTitle,
                $seoDescription,
                $seoText,
                $seoTextHidden,
                $seoTextImage,
                $cardTitle,
                $cardDescription,
                $cardImage,
                $cardPrice,
                $thumbImage,
                $template,
                $updated,
            ]
        );

        return $connection->lastInsertId();
    }

    public static function getContentByPath(Connection $connection, string $path): int
    {
        $contentId = (int)($connection->executeQuery(
            'SELECT id FROM content where path=?', [$path])
            ->fetchOne());
        if (0 === $contentId) {
            throw new RuntimeException(sprintf('Не удалось найти страницу с путем "%s"',
                $path));
        }

        return $contentId;
    }
}