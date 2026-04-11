-- Update church coordinates to accurate latitude and longitude
/*USE sadiwa_db;*/

-- Update Baclayon Church
UPDATE Churches SET 
    name = 'Baclayon Church (Immaculate Conception Church)',
    latitude = 9.6178, 
    longitude = 123.9147 
WHERE name LIKE '%Baclayon%';

-- Update Loboc Church
UPDATE Churches SET 
    name = 'Loboc Church (San Pedro Apostol Parish)',
    latitude = 9.6364, 
    longitude = 124.0308 
WHERE name LIKE '%Loboc%';

-- Update Maribojoc Church
UPDATE Churches SET 
    name = 'Maribojoc Church (Santa Cruz Parish)',
    latitude = 9.7514, 
    longitude = 123.8294 
WHERE name LIKE '%Maribojoc%';

-- Update Loon Church
UPDATE Churches SET 
    name = 'Loon Church (Our Lady of Light Parish)',
    latitude = 9.8019, 
    longitude = 123.7931 
WHERE name LIKE '%Loon%';

-- Update Dauis Church
UPDATE Churches SET 
    name = 'Dauis Church (Our Lady of Assumption)',
    latitude = 9.6336, 
    longitude = 123.8528 
WHERE name LIKE '%Dauis%';

-- Update Panglao Church
UPDATE Churches SET 
    name = 'Panglao Church (San Agustin Parish)',
    latitude = 9.5842, 
    longitude = 123.7536 
WHERE name LIKE '%Panglao%';

-- Update Tagbilaran Cathedral
UPDATE Churches SET 
    name = 'Tagbilaran Cathedral (St. Joseph the Worker)',
    latitude = 9.6486, 
    longitude = 123.8539 
WHERE name LIKE '%Tagbilaran%';

-- Update Dimiao Church
UPDATE Churches SET 
    name = 'Dimiao Church (St. Nicholas de Tolentino)',
    latitude = 9.6139, 
    longitude = 124.3278 
WHERE name LIKE '%Dimiao%';

-- Update Jagna Church
UPDATE Churches SET 
    name = 'Jagna Church (San Miguel Archangel Parish)',
    latitude = 9.6511, 
    longitude = 124.3656 
WHERE name LIKE '%Jagna%';

-- Update Alburquerque Church
UPDATE Churches SET 
    name = 'Alburquerque Church (San Jose Parish)',
    latitude = 9.6308, 
    longitude = 123.9644 
WHERE name LIKE '%Alburquerque%';

-- Update Loay Church
UPDATE Churches SET 
    name = 'Loay Church (Our Lady of Guadalupe)',
    latitude = 9.5928, 
    longitude = 123.9997 
WHERE name LIKE '%Loay%';

-- Update Cortes Church
UPDATE Churches SET 
    name = 'Cortes Church (San Roque Parish)',
    latitude = 9.7147, 
    longitude = 124.0039 
WHERE name LIKE '%Cortes%';

SELECT 'Coordinates updated successfully!' as status;
