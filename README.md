# tree_category


Create database and create table

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    parent_path VARCHAR(255) NOT NULL
);

Create index
ALTER TABLE categories ADD INDEX idx_parent_path (parent_path)

Example Data
Given the same hierarchy, the table might look like this:

id	name	            parent_path
1	Root	            /
2	Electronics	        /1/
3	Mobile Phones	    /1/2/
4	Laptops	            /1/2/
5	Home Appliances	    /1/
6	Refrigerators	    /1/5/
7	Washing Machines	/1/5/

Operations

- Insertion
To insert a new node, you need to determine its parent_path based on its parent. For example, to insert a new subcategory under "Mobile Phones":

    INSERT INTO categories (name, parent_path)
    VALUES ('Smartphones', '/1/2/3/');

- Retrieval
To retrieve a subtree, you can use the LIKE operator to match the parent_path:

    SELECT * FROM categories WHERE parent_path LIKE '/1/2/%';
This query retrieves all categories under "Electronics".

- Deletion
Deleting a node and its subtree can be done using the LIKE operator:

    DELETE FROM categories WHERE parent_path LIKE '/1/2/%';
This deletes "Electronics" and all its subcategories.

- Updating Node Path
Updating a node's parent_path and ensuring the consistency of the subtree requires updating the parent_path of all its descendants. Here's how you can do it:

- Find the current parent_path of the node.
Update the parent_path of the node.
Update the parent_path of all descendants.
For example, if we want to move "Mobile Phones" (id 3) under "Home Appliances" (id 5), we need to:

Find the current parent_path of "Mobile Phones":
    SELECT parent_path FROM categories WHERE id = 3;

Update the parent_path of "Mobile Phones":
    UPDATE categories SET parent_path = '/1/5/3/' WHERE id = 3;

Update the parent_path of all descendants of "Mobile Phones":
    UPDATE categories
    SET parent_path = REPLACE(parent_path, '/1/2/3/', '/1/5/3/')
    WHERE parent_path LIKE '/1/2/3/%';


Advantages
Simplicity: Still easy to implement and understand.
Read Efficiency: Efficient for read operations, especially for retrieving entire subtrees.
Clear Separation: Clear separation of node id and parent_path.

Disadvantages
Update Complexity: Renaming or moving nodes requires updating the parent_path values for the node and all its descendants.
Storage Overhead: The parent_path string can become long and consume more storage, especially in deep hierarchies.

Use Cases
This adjusted Path Enumeration Model is still suitable for applications where the hierarchy is relatively static and read-heavy. It provides efficient retrieval of entire subtrees but can become cumbersome for applications with frequent updates to the hierarchy.
