import {useEffect, useState} from "react";
import UserApi from "@/api/authenticated/user.js";
import {useStateContext} from "@/context/ContextProvider.jsx";

const UserModal = ({ isUserModalOpen, closeUserModal, formType, userId, resetUser }) => {
  const [loading, setLoading] = useState(1)
  const [disabled, setDisabled] = useState(0)
  const {setNotification} = useStateContext()
  const [user, setUser] = useState({
    id: null,
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
  })

  useEffect(() => {
    if (isUserModalOpen) {
      console.log('here aaa')
      if (!userId && formType == "Create" ) {
        console.log('am i here', formType)
        setUser({
          id: null,
          name: '',
          email: '',
          password: '',
          password_confirmation: ''
        });
        setLoading(0)
      }
    
      if (userId && formType == "Edit") {
        console.log('or here', formType)
        if (!user.id) {
          UserApi.show(`${userId}`)
          .then(({data}) => {
            setUser(data)
            setLoading(0)
          }).catch(()=>{
            setLoading(0)
          })
        }
      }
    }
  }, [isUserModalOpen])
  

  const closeModalAndSetUserEmpty = () => {
    setLoading(1)
    setDisabled(0)
    setUser({
      id: null,
      name: '',
      email: '',
      password: '',
      password_confirmation: ''
    });
    closeUserModal();
  }

  const onSubmit = ev => {
    ev.preventDefault()
    setDisabled(1)
    if (user.id) {
      UserApi.update(`${user.id}`, user)
        .then(() => {
          setNotification('User was successfully updated')
          closeModalAndSetUserEmpty()
          resetUser()
        })
        .catch(err => {
          const response = err.response;
          if (response && response.status === 422) {
            setErrors(response.data.errors)
          }
        })
    } else {
      UserApi.store(user)
        .then(() => {
          setNotification('User was successfully created')
          closeModalAndSetUserEmpty()
          resetUser()
        })
        .catch(err => {
          const response = err.response;
          if (response && response.status === 422) {
            setErrors(response.data.errors)
          }
        })
    }
  }

  return (
    <div>
      {isUserModalOpen && loading && (
        
        <div className="modal">
          
          <div className="modal-content">
            <h2>
              {
                formType ? "Edit Form" : "Create Form"
              }
            </h2>
            { loading &&
              <div>
                <img src="public/loading.gif" alt="Logo" className="logo" />
              </div>
            }
          </div>
        </div>
      )}

      {isUserModalOpen && !loading && (
        <div className="modal">
          
          <div className="modal-content">
            <h2>
              {
                formType ? "Edit Form" : "Create Form"
              }
            </h2>
            <div>
              <form>
                <input value={user.name} onChange={ev => setUser({...user, name: ev.target.value})} placeholder="Name" disabled={disabled}/>
                <input value={user.email} onChange={ev => setUser({...user, email: ev.target.value})} placeholder="Email" disabled={disabled}/>
                <input type="password" onChange={ev => setUser({...user, password: ev.target.value})} placeholder="Password" disabled={disabled} />
                <input type="password" onChange={ev => setUser({...user, password_confirmation: ev.target.value})} placeholder="Password Confirmation" disabled={disabled} />
              </form>
            </div>
            
            <div className="modal-actions">
              <button className="btn-edit" onClick={onSubmit}>
                {disabled ? <img src="public/loading.gif" alt="Logo" className="logo" /> : "Confirm"}
              </button>
              <button className="btn-delete" onClick={ev => closeUserModal && closeModalAndSetUserEmpty()}>
                {disabled ? <img src="public/loading.gif" alt="Logo" className="logo" /> : "Close"}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default UserModal;
