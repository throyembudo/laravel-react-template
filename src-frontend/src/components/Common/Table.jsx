import {Link} from "react-router-dom";

const Table = ({ columns, data, columnsLength, loading, emitRow, pages, getUserByPage, openCreateModal }) => {
  
  return (
    <div className="card animated fadeInDown">
      <table>
        <thead>
          <tr>
            {columns.map((column, index) => (
              <th key={index}>{column.header}</th>
            ))}
          </tr>
        </thead>
        {loading &&
          <tbody>
          <tr>
            <td colSpan={columnsLength} className="text-center">
              <img src="public/loading.gif" alt="Logo" className="logo" />
            </td>
          </tr>
          </tbody>
        }
        {!loading &&
          <tbody>
          {data.map((row, rowIndex) => (
            <tr key={rowIndex}>
              {columns.map((column, colIndex) => (
                column.accessor !== "actions" ? (
                  <td key={colIndex}>{row[column.accessor]}</td>
                ) : (
                  column.accessor === "actions" && (
                    <td key={colIndex}>
                      {column.buttons && column.buttons.length > 0 && (
                        <div>
                          <button className="btn-edit" onClick={ev => openCreateModal && openCreateModal("Edit", row.id)}>Edit</button>
                          &nbsp;
                          <button className="btn-delete" onClick={ev => emitRow && emitRow(row)}>Delete</button>
                        </div>
                      )}
                    </td>
                  )
                )
              ))}
            </tr>
          ))}
          </tbody>
        }
      </table>
      { pages && !loading &&
        <ul className="label-ul">
        {pages.map((page, index) => {
          let cleanedItem = page.label.replace(/&raquo;/g, "");
          cleanedItem = cleanedItem.replace(/&laquo;/g, "");
          return <button className={`label-page ${page.active ? "btn-edit" : "btn-default"}`} key={index} onClick={ev => getUserByPage && getUserByPage(page.url)}>{cleanedItem.trim()}</button>
        })}
        </ul>
      }
    </div>
  );
};

export default Table;