const Modal = ({ isModalOpen, userDetail, deleteUser, closeModal }) => {
  
  return (
    <div>
      {isModalOpen && (
        <div className="modal">
          <div className="modal-content">
            <h2>Confirmation</h2>
            <div>
              Are you sure you want to delete this user? <br />
              <strong> User: { userDetail.name } </strong>
            </div>
            <div className="modal-actions">
              <button className="btn-edit" onClick={deleteUser}>Yes</button>
              <button className="btn-delete" onClick={closeModal}>No</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default Modal;