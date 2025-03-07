<?php
namespace App\Livewire;

use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use App\Models\Post;
use Illuminate\Support\Str;


class Posts extends Component
{
    use WithFileUploads;

    public $title, $description, $photo, $post_id;
    public $isOpen = 0;
    public $search;
    public $rowperPage = 10;
    /**
     * Render the component with the posts and pagination
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {

        // Return the view with posts and pagination data
        return view('livewire.posts',[
            'posts' => $this->search === null ?
            Post::latest()->paginate($this->rowperPage) :
            Post::latest()->where('title', 'like', '%'.$this->search.'%')->paginate($this->rowperPage)
        ] );
    }

    public function searchPosts()
    {
    $this->render();
    }


    /**
     * Open the modal for creating a new post
     *
     * @return void
     */
    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    /**
     * Open the modal for creating or editing a post
     *
     * @return void
     */
    public function openModal()
    {
        $this->isOpen = true;
    }

    /**
     * Close the modal
     *
     * @return void
     */
    public function closeModal()
    {
        $this->isOpen = false;
    }

    /**
     * Reset the input fields for the post
     *
     * @return void
     */
    private function resetInputFields()
    {
        $this->photo = null;
        $this->title = '';
        $this->description = '';
        $this->post_id = '';
    }

    /**
     * Store the post (either create or update)
     *
     * @return void
     */
    public function store()
    {
        $this->validate([
            'title' => 'required',
            'description' => 'required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($this->photo) { //membuat nama gambar
            $photoName = \Str::slug($this->title,'-')
            .'-'
            .uniqid()
            .'-'.$this->photo->getClientOriginalExtension();
            $photoPath = $this->photo->storeAs('posts',$photoName,'public'); // Simpan ke storage/public/posts
        }


        // Create or update the post
        Post::updateOrCreate(['id' => $this->post_id], [
            'title' => $this->title,
            'photo' => $photoPath,
            'description' => $this->description,

        ]);

        // Flash success message to the session
        session()->flash('message',
            $this->post_id ? 'Post Updated Successfully.' : 'Post Created Successfully.');

        // Close the modal and reset the form fields
        $this->closeModal();
        $this->reset(['title', 'description', 'photo', 'post_id']);
    }

    /**
     * Edit the post by finding it by ID
     *
     * @param int $id
     * @return void
     */
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $this->post_id = $id;
        $this->photo = $post->photo;
        $this->title = $post->title;
        $this->description = $post->description;

        $this->openModal();
    }

    /**
     * Delete the post by ID
     *
     * @param int $id
     * @return void
     */
    public function delete($id)
    {
        if($post && $post->$photo){
            $photoPath = "public/" . $post->$photo;
            if (Storage::exists($photoPath)) {
            Storage::delete($photoPath);
            }
        }
        Post::find($id)->delete();
        session()->flash('message', 'Post Deleted Successfully.');
        $this->closeModal();
    }
}
