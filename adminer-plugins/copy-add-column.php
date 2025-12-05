<?php

/**
 * Plugin Copy ADD COLUMN SQL
 * 
 * Adiciona um botão ao lado de cada coluna na estrutura da tabela
 * que copia o comando SQL ALTER TABLE ADD COLUMN para a área de transferência.
 * 
 * @author Plugin personalizado
 * @license MIT
 */

namespace Adminer;

class AdminerCopyAddColumn
{

  /**
   * Sobrescreve a impressão da estrutura da tabela para adicionar botão de copiar SQL
   */
  function tableStructurePrint($fields, $tableStatus = null)
  {
    // Obtém o nome da tabela
    $tableName = isset($tableStatus["Name"]) ? $tableStatus["Name"] : (isset($_GET["table"]) ? $_GET["table"] : "");

    echo "<div class='scrollable'>\n";
    echo "<table class='nowrap odds'>\n";
    echo "<thead><tr>";
    echo "<th>" . lang(48) . "</th>";
    echo "<td>" . lang(49) . "</td>";
    if (support("comment")) {
      echo "<td>" . lang(50) . "</td>";
    }
    echo "<td>SQL</td>";
    echo "</tr></thead>\n";

    $structuredTypes = driver()->structuredTypes();

    foreach ($fields as $field) {
      echo "<tr>";
      echo "<th>" . h($field["field"]) . "</th>";

      // Tipo da coluna
      $type = h($field["full_type"]);
      $collation = h($field["collation"]);

      echo "<td>";
      echo "<span title='" . $collation . "'>";
      if (in_array($type, (array) $structuredTypes[lang(6)])) {
        echo "<a href='" . h(ME . 'type=' . urlencode($type)) . "'>" . $type . "</a>";
      } else {
        echo $type;
        if ($collation && isset($tableStatus["Collation"]) && $collation != $tableStatus["Collation"]) {
          echo " " . $collation;
        }
      }
      echo "</span>";

      // NULL
      if ($field["null"]) {
        echo " <i>NULL</i>";
      }

      // AUTO_INCREMENT
      if ($field["auto_increment"]) {
        echo " <i>" . lang(51) . "</i>";
      }

      // DEFAULT
      $default = h($field["default"]);
      if (isset($field["default"])) {
        echo " <span title='" . lang(52) . "'>[<b>";
        if ($field["generated"]) {
          echo "<code class='jush-" . JUSH . "'>" . $default . "</code>";
        } else {
          echo $default;
        }
        echo "</b>]</span>";
      }

      echo "</td>";

      // Comentário
      if (support("comment")) {
        echo "<td>" . h($field["comment"]) . "</td>";
      }

      // Gera o SQL ALTER TABLE ADD COLUMN
      $addColumnSql = $this->generateAddColumnSql($tableName, $field);

      // Botão para copiar o SQL
      echo "<td>";
      echo "<button type='button' class='copysql' data-sql='" . h($addColumnSql) . "' title='Copiar ADD COLUMN SQL'>📋</button>";
      echo "</td>";

      echo "</tr>\n";
    }

    echo "</table>\n";
    echo "</div>\n";

    // Adiciona o JavaScript para copiar para a área de transferência
    echo script('
            // Cria o elemento toast se não existir
            if (!document.getElementById("copyToast")) {
                var toast = document.createElement("div");
                toast.id = "copyToast";
                toast.className = "toast";
                toast.textContent = "✓ SQL copiado para a área de transferência!";
                document.body.appendChild(toast);
            }
            
            function showToast() {
                var toast = document.getElementById("copyToast");
                toast.classList.add("show");
                setTimeout(function() {
                    toast.classList.remove("show");
                }, 2000);
            }
            
            document.querySelectorAll(".copysql").forEach(function(btn) {
                btn.onclick = function() {
                    var sql = this.dataset.sql;
                    var button = this;
                    navigator.clipboard.writeText(sql).then(function() {
                        button.textContent = "✓";
                        showToast();
                        setTimeout(function() {
                            button.textContent = "📋";
                        }, 1000);
                    }).catch(function(err) {
                        alert("Erro ao copiar: " + err);
                    });
                };
            });
        ');
  }

  /**
   * Gera o comando SQL ALTER TABLE ADD COLUMN
   */
  private function generateAddColumnSql($tableName, $field)
  {
    $sql = "ALTER TABLE " . idf_escape($tableName) . " ADD COLUMN " . idf_escape($field["field"]);
    $sql .= " " . $field["full_type"];

    // NOT NULL
    if (!$field["null"]) {
      $sql .= " NOT NULL";
    }

    // DEFAULT
    if (isset($field["default"]) && $field["default"] !== null) {
      if ($field["generated"]) {
        $sql .= " DEFAULT " . $field["default"];
      } else {
        $sql .= " DEFAULT " . q($field["default"]);
      }
    }

    // AUTO_INCREMENT
    if (!empty($field["auto_increment"])) {
      $sql .= " AUTO_INCREMENT";
    }

    // COMMENT
    if (!empty($field["comment"])) {
      $sql .= " COMMENT " . q($field["comment"]);
    }

    $sql .= ";";

    return $sql;
  }
}
