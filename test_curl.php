<?php
echo "Fetching: http://localhost:8000/file/profiles/8apV5Ej3Xgp9AbR7Rw25V9ixS38G4bXakt0z6ijw.png\n";
$headers = get_headers('http://localhost:8000/file/profiles/8apV5Ej3Xgp9AbR7Rw25V9ixS38G4bXakt0z6ijw.png', 1);
print_r($headers);
