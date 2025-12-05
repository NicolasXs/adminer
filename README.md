# Adminer Plugin: Copy ADD COLUMN SQL

Plugin for [Adminer](https://www.adminer.org/) that adds a button next to each column in the table structure view, allowing you to copy the `ALTER TABLE ADD COLUMN` SQL command to the clipboard.

## ✨ Features

- Adds a 📋 button next to each column in the table structure view
- Clicking it automatically copies the complete SQL command to add that column to another table
- Useful for quickly replicating column structures between tables

## 📸 Demo
![Gravação-de-tela-de-2025-12-05-16-01-15](https://github.com/user-attachments/assets/20d8c863-023a-4971-84b1-94019d028b05)

## 🚀 Installation

1. Download the plugin files
2. Place the `adminer-plugins` folder in the same directory as Adminer
3. Configure the `adminer-plugins.php` file to load the plugin
4. Access Adminer as usual

## 📁 Structure

```
adminer/
├── index.php              # Main Adminer file
├── adminer-plugins.php    # Plugin configuration
├── adminer.css            # Custom styles
└── adminer-plugins/
    └── copy-add-column.php # Copy SQL plugin
```
