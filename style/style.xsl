<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">
  <html>
  <head>
    <style>
      table {
        border-collapse: collapse;
        width: 100%;
      }

      th, td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
      }

      th {
        background-color: #f2f2f2;
      }
    </style>
  </head>
  <body>
    <h2>Deliberations</h2>
    <table>
      <tr>
        <th>Nom Fichier</th>
        <th>Date Texte</th>
      </tr>
      <xsl:for-each select="data/deliberation">
        <tr>
          <td><xsl:value-of select="nom_fichier"/></td>
          <td><xsl:value-of select="date_texte"/></td>
        </tr>
      </xsl:for-each>
    </table>
  </body>
  </html>
</xsl:template>

</xsl:stylesheet>
