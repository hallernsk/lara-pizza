INSERT INTO products (
    id,
    name,
    description,
    price,
    image,
    type,
    created_at,
    updated_at
  )
VALUES (
    'id:bigint',
    'name:varchar',
    'description:text',
    'price:decimal',
    'image:varchar',
    'type:enum',
    'created_at:timestamp',
    'updated_at:timestamp'
  );