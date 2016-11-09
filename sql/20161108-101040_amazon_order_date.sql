USE bte;

ALTER TABLE `amazon_order`
    ADD COLUMN `CreatedOn` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `IsPremiumOrder`,
    ADD COLUMN `UpdatedOn` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `CreatedOn`;

ALTER TABLE `amazon_order_item`
    ADD COLUMN `CreatedOn` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `ConditionNote`,
    ADD COLUMN `UpdatedOn` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `CreatedOn`;
