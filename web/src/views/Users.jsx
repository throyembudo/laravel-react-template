import {useEffect, useState} from "react";
import UserApi from "@/api/authenticated/user.js";
import {Link} from "react-router-dom";
import {useStateContext} from "../context/ContextProvider.jsx";
import Table from "@/components/Common/Table";
import DeleteModal from "@/components/Common/DeleteModal";

export default function Users() {
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(false);
  const {setNotification} = useStateContext();
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [userDetail, setUserDetail] = useState([]);
  const [pages, setPages] = useState([]);

  const closeModal = () => {
    setIsModalOpen(false);
    setUserDetail([]);
  };

  const deleteUser = () => {
    setLoading(true)
    UserApi.destroy(`${userDetail.id}`)
      .then(() => {
        setNotification('User was successfully deleted')
        getUsers()
        setLoading(false)
      })
      .catch(() => {
        setLoading(false)
      })
    setUserDetail([]);
    closeModal();
  };

  const columns = [
    { header: "ID", accessor: "id" },
    { header: "Name", accessor: "name" },
    { header: "Email", accessor: "email" },
    { header: "Create Date", accessor: "created_at" },
    { header: "Actions", accessor: "actions", buttons: [
      {name: "Edit", link: "/users/"},
      {name: "Delete", link: ""}
     ] 
    }
  ];

  useEffect(() => {
    getUsers();
  }, [])

  const onDeleteClick = user => {
    setIsModalOpen(true);
    setUserDetail(user);
  }

  const getUsers = (url) => {
    setLoading(true)
    UserApi.index()
      .then(({ data }) => {
        setLoading(false)
        setUsers(data.data)
        setPages(data.meta.links)
        console.log(data.meta.links)
      })
      .catch(() => {
        setLoading(false)
      })
  }

  const getUserByPage = (url) => {
    setLoading(true)
    UserApi.index(url)
      .then(({ data }) => {
        setLoading(false)
        setUsers(data.data)
        setPages(data.meta.links)
      })
      .catch(() => {
        setLoading(false)
      })
  }

  return (
    <div>
      <div style={{display: 'flex', justifyContent: "space-between", alignItems: "center"}}>
        <h1>Users</h1>
        <Link className="btn-add" to="/users/new">Add new</Link>
      </div>
      <div className="card animated fadeInDown">
        <Table 
          columns={columns} 
          data={users} 
          columnsLength={columns.length} 
          loading={loading}  
          pages={pages}
          emitRow={onDeleteClick} 
          getUserByPage={getUserByPage} 
        ></Table>
      </div>
      <DeleteModal
        userDetail={userDetail}
        isModalOpen={isModalOpen}
        deleteUser={deleteUser}
        closeModal={closeModal}
      >
      </DeleteModal>
    </div>
  )
}
